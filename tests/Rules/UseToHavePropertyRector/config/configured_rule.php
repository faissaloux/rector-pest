<?php

declare(strict_types=1);

use MrPunyapal\RectorPest\Rules\UseToHavePropertyRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([UseToHavePropertyRector::class]);
