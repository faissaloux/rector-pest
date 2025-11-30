<?php

declare(strict_types=1);

use MrPunyapal\RectorPest\Rules\UseToBeDirectoryRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([UseToBeDirectoryRector::class]);
