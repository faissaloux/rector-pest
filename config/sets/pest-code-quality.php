<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

/**
 * Code quality improvements for Pest tests
 *
 * This set will contain rules for:
 * - Better test readability and expressiveness
 * - Removing redundant code in tests
 * - Using more expressive Pest APIs (expect, toBe, etc.)
 * - Type safety improvements
 * - Dataset optimizations
 * - Better assertion methods
 */
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');

    // Rules will be added here
    // Example: $rectorConfig->rule(UseExpectOverAssertRector::class);
    // Example: $rectorConfig->rule(SimplifyDatasetRector::class);
};
