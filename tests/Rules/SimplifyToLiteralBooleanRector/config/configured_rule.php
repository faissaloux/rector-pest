<?php

declare(strict_types=1);

use MrPunyapal\RectorPest\Rules\SimplifyToLiteralBooleanRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(SimplifyToLiteralBooleanRector::class);
};
