<?php

declare(strict_types=1);

namespace MrPunyapal\RectorPest\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use MrPunyapal\RectorPest\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Example rector rule placeholder
 * This file demonstrates the structure for future Pest rector rules
 *
 * @see \MrPunyapal\RectorPest\Tests\Rector\MethodCall\ExampleRector\ExampleRectorTest
 */
final class ExampleRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Example rule description',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
// Before
test()->example();
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
// After
test()->improved();
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
        // Rule implementation will go here
        return null;
    }
}