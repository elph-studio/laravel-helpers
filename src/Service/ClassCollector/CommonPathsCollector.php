<?php

declare(strict_types=1);

namespace Elph\LaravelHelpers\Service\ClassCollector;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

abstract class CommonPathsCollector extends ClassCollector
{
    private const array COMMON_PATHS_CONFIG_LOCATION = [
        'vendor/elph-studio/laravel-helpers/src/Config/common_paths.php',
        'src/Config/common_paths.php',
        'config/common_paths.php',
    ];

    protected function getCommonPathsConfig(string $entity): Collection
    {
        $config = config('common_paths', []);
        if (Arr::has($config, $entity) === true) {
            return collect($config[$entity]);
        }

        $config = [];
        collect(self::COMMON_PATHS_CONFIG_LOCATION)
            ->each(function (string $path) use (&$config, $entity) {
                if (File::exists($path) === false) {
                    return true;
                }

                $fullConfig = require $path;
                if (empty($fullConfig) === true || Arr::has($fullConfig, $entity) === false) {
                    return false;
                }

                $config = $fullConfig[$entity];

                return false;
            });

        return collect($config);
    }
}
