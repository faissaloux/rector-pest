<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

/**
 * Easy Coding Standard configuration for rector-pest
 * Ensures code quality and consistent formatting
 */
return ECSConfig::configure()
    ->withPreparedSets(psr12: true, common: true)
    ->withPaths([
        __DIR__ . '/config',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withRootFiles()
    ->withSkip([
        '*/Source/*',
        '*/Fixture/*',
        '*/Expected/*',
    ]);
