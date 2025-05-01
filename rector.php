<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;

return RectorConfig::configure()
    ->withConfiguredRule(RectorLaravel\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector::class, [
        'exclude_methods' => [
            '*',
        ],
    ])
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/bootstrap/app.php',
        __DIR__ . '/config',
        __DIR__ . '/database/seeders',
        __DIR__ . '/database/factories',
        __DIR__ . '/public',
    ])
    ->withSkip([
        AddOverrideAttributeToOverriddenMethodsRector::class,
    ])

    ->withPreparedSets(
        // deadCode: true,
        // codeQuality: true,
        // typeDeclarations: true,
        // privatization: true,
        // earlyReturn: true,
        // strictBooleans: true,
    )
    ->withPhpSets();
