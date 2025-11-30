<?php

declare(strict_types=1);

namespace MrPunyapal\RectorPest\Rules;

use MrPunyapal\RectorPest\AbstractRector;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class SimplifyExpectNotRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            "Simplifies negated expectations by using Pest's built-in not modifier instead of manual negations",
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
expect(!$condition)->toBeTrue();
expect(!$value)->toBe(false);
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
expect($condition)->not->toBeTrue();
expect($value)->not->toBe(false);
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
        if (! $this->isExpectChain($node)) {
            return null;
        }

        $expectCall = $this->getExpectFuncCall($node);
        if (! $expectCall instanceof FuncCall) {
            return null;
        }

        if (! isset($expectCall->args[0])) {
            return null;
        }

        $arg = $expectCall->args[0];
        if (! $arg instanceof Arg) {
            return null;
        }

        if (! $arg->value instanceof BooleanNot) {
            return null;
        }

        $negatedExpression = $arg->value->expr;

        $expectCall->args[0] = $this->nodeFactory->createArg($negatedExpression);

        return $this->addNotModifier($node);
    }

    private function addNotModifier(MethodCall $methodCall): MethodCall
    {
        $expectCall = $this->getExpectFuncCall($methodCall);
        if (! $expectCall instanceof FuncCall) {
            return $methodCall;
        }

        $methods = $this->collectChainMethods($methodCall);
        $notProperty = new PropertyFetch($expectCall, 'not');

        $result = $this->rebuildMethodChain($notProperty, $methods);

        /** @var MethodCall $result */
        return $result;
    }
}
