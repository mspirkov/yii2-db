<?php

declare(strict_types=1);

use MSpirkov\Yii2\Rector\Yii2SetList;
use Rector\CodeQuality\Rector\BooleanAnd\SimplifyEmptyArrayCheckRector;
use Rector\CodeQuality\Rector\Empty_\SimplifyEmptyCheckOnEmptyArrayRector;
use Rector\CodeQuality\Rector\FuncCall\SortCallLikeNamedArgsRector;
use Rector\CodeQuality\Rector\FunctionLike\SimplifyUselessVariableRector;
use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\CodeQuality\Rector\If_\SimplifyIfReturnBoolRector;
use Rector\CodeQuality\Rector\Isset_\IssetOnPropertyObjectToPropertyExistsRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector;
use Rector\DeadCode\Rector\PropertyProperty\RemoveNullPropertyInitializationRector;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\Empty_\EmptyOnNullableObjectToInstanceOfRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withImportNames()
    ->withPhpLevel(20)
    ->withSets([
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::PRIVATIZATION,
        SetList::TYPE_DECLARATION,
        Yii2SetList::MAIN,
    ])
    ->withPHPStanConfigs([
        __DIR__ . '/phpstan.dist.neon',
    ])
    ->withSkip([
        // PHP 7.4
        ClosureToArrowFunctionRector::class,
        // Code Quality
        FlipTypeControlToUseExclusiveTypeRector::class,
        IssetOnPropertyObjectToPropertyExistsRector::class,
        SimplifyEmptyArrayCheckRector::class,
        SimplifyEmptyCheckOnEmptyArrayRector::class,
        SortCallLikeNamedArgsRector::class,
        SimplifyIfReturnBoolRector::class,
        // Dead Code
        SimplifyUselessVariableRector::class,
        RemoveNullPropertyInitializationRector::class,
        RemoveUselessParamTagRector::class,
        // Type Declarations
        EmptyOnNullableObjectToInstanceOfRector::class,
    ]);
