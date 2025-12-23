<?php

declare(strict_types=1);

namespace RectorPest\Rules\Pest2ToPest3;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\VariadicPlaceholder;
use PHPStan\Reflection\ReflectionProvider;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Converts pest()->uses() to pest()->extend() for classes and pest()->use() for traits.
 *
 * Before: pest()->uses(TestCase::class, RefreshDatabase::class)->in('Feature')
 * After:  pest()->extend(TestCase::class)->use(RefreshDatabase::class)->in('Feature')
 */
final class UsesToExtendRector extends AbstractRector
{
    public function __construct(
        private readonly ReflectionProvider $reflectionProvider
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Converts pest()->uses() to pest()->extend() for classes and pest()->use() for traits',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
pest()->uses(Tests\TestCase::class)->in('Feature');
pest()->uses(Illuminate\Foundation\Testing\RefreshDatabase::class);
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
pest()->extend(Tests\TestCase::class)->in('Feature');
pest()->use(Illuminate\Foundation\Testing\RefreshDatabase::class);
CODE_SAMPLE
                ),
            ]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param MethodCall $node
     */
    public function refactor(Node $node): ?Node
    {
        // We only want to process the outermost method call of a pest()->uses() chain
        // Find uses() in the chain
        $usesCall = $this->findUsesCallInChain($node);
        if ($usesCall === null) {
            return null;
        }

        // Make sure this is the outermost call - we check if the node IS the uses call
        // (simple case without chain) OR node's var eventually leads to uses
        if (! $this->isDirectUsesCall($node) && ! $this->hasUsesInChain($node)) {
            return null;
        }

        // Skip if this node IS NOT the outermost - check if parent has this as var
        // We handle this by only processing when uses() IS the current node OR
        // when current node is the outermost of a uses chain
        
        // For direct uses() call: pest()->uses(X) 
        if ($this->isName($node->name, 'uses') && $this->isPestChain($node)) {
            return $this->transformUsesCall($node, $node, []);
        }

        // For chained: pest()->uses(X)->in('Feature') - only process the outermost
        if ($this->hasUsesInChain($node)) {
            // Collect the method chain from node back to uses()
            $methodsAfter = $this->collectMethodsUntilUses($node);
            return $this->transformUsesCall($node, $usesCall, $methodsAfter);
        }

        return null;
    }

    /**
     * Check if node is a direct uses() call on pest()
     */
    private function isDirectUsesCall(MethodCall $node): bool
    {
        return $this->isName($node->name, 'uses') && $this->isPestChain($node);
    }

    /**
     * Check if uses() exists somewhere in the var chain
     */
    private function hasUsesInChain(MethodCall $node): bool
    {
        $current = $node->var;
        
        while ($current instanceof MethodCall) {
            if ($this->isName($current->name, 'uses') && $this->isPestChain($current)) {
                return true;
            }
            $current = $current->var;
        }

        return false;
    }

    /**
     * Find the uses() call anywhere in the chain
     */
    private function findUsesCallInChain(MethodCall $node): ?MethodCall
    {
        // First check if this node is uses()
        if ($this->isName($node->name, 'uses') && $this->isPestChain($node)) {
            return $node;
        }

        // Search in the var chain
        $current = $node->var;
        
        while ($current instanceof MethodCall) {
            if ($this->isName($current->name, 'uses') && $this->isPestChain($current)) {
                return $current;
            }
            $current = $current->var;
        }

        return null;
    }

    /**
     * Collect methods from outermost until uses() (not including uses)
     *
     * @return array<array{name: Identifier|Expr, args: array<Arg|VariadicPlaceholder>}>
     */
    private function collectMethodsUntilUses(MethodCall $outermost): array
    {
        $methods = [];
        $current = $outermost;

        while ($current instanceof MethodCall) {
            if ($this->isName($current->name, 'uses')) {
                break;
            }
            $methods[] = [
                'name' => $current->name,
                'args' => $current->args,
            ];
            $current = $current->var instanceof MethodCall ? $current->var : null;
        }

        // Reverse to get correct order
        return array_reverse($methods);
    }

    /**
     * Transform the uses() call into extend()/use() calls
     *
     * @param array<array{name: Identifier|Expr, args: array<Arg|VariadicPlaceholder>}> $methodsAfter
     */
    private function transformUsesCall(MethodCall $outermost, MethodCall $usesCall, array $methodsAfter): ?Node
    {
        if ($usesCall->args === []) {
            return null;
        }

        // Separate classes and traits
        $classes = [];
        $traits = [];

        foreach ($usesCall->args as $arg) {
            if (! $arg instanceof Arg) {
                continue;
            }

            $classConstFetch = $arg->value;
            if (! $classConstFetch instanceof ClassConstFetch) {
                continue;
            }

            if (! $classConstFetch->class instanceof Name) {
                continue;
            }

            $className = $classConstFetch->class->toString();

            if ($this->reflectionProvider->hasClass($className)) {
                $classReflection = $this->reflectionProvider->getClass($className);

                if ($classReflection->isTrait()) {
                    $traits[] = new Arg($classConstFetch);
                } else {
                    $classes[] = new Arg($classConstFetch);
                }
            } else {
                // If we can't resolve, default to class (extend)
                $classes[] = new Arg($classConstFetch);
            }
        }

        // If no arguments were processed
        if ($classes === [] && $traits === []) {
            return null;
        }

        // Get the pest() function call
        $pestCall = $this->getPestFuncCall($usesCall);
        if (! $pestCall instanceof FuncCall) {
            return null;
        }

        // Build the new chain starting from pest()
        $result = $pestCall;

        // Add extend() for classes first
        if ($classes !== []) {
            $result = new MethodCall($result, 'extend', $classes);
        }

        // Add use() for traits
        if ($traits !== []) {
            $result = new MethodCall($result, 'use', $traits);
        }

        // Re-add the methods that came after uses() (like ->in())
        foreach ($methodsAfter as $method) {
            $result = new MethodCall($result, $method['name'], $method['args']);
        }

        return $result;
    }

    /**
     * Check if a method call is part of a pest() chain
     */
    private function isPestChain(MethodCall $methodCall): bool
    {
        $current = $methodCall->var;

        while ($current instanceof MethodCall) {
            $current = $current->var;
        }

        if ($current instanceof FuncCall) {
            return $this->isName($current, 'pest');
        }

        return false;
    }

    /**
     * Get the pest() function call from a method chain
     */
    private function getPestFuncCall(MethodCall $methodCall): ?FuncCall
    {
        $current = $methodCall->var;

        while ($current instanceof MethodCall) {
            $current = $current->var;
        }

        if ($current instanceof FuncCall && $this->isName($current, 'pest')) {
            return $current;
        }

        return null;
    }
}
