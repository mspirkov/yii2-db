<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Instanceof_\Rector\Ternary\FlipNegatedTernaryInstanceofRector;
use Rector\Set\ValueObject\SetList;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withImportNames()
    ->withPhpVersion(PhpVersion::PHP_74)
    ->withPhp74Sets()
    ->withSets([
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::PRIVATIZATION,
        SetList::TYPE_DECLARATION,
    ])
    ->withPHPStanConfigs([
        __DIR__ . '/phpstan.dist.neon',
    ])
    ->withRules([
        FlipNegatedTernaryInstanceofRector::class,
    ]);
