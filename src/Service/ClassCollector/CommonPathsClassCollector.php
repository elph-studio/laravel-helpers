<?php

declare(strict_types=1);

namespace Elph\LaravelHelpers\Service\ClassCollector;

use Illuminate\Support\Collection;

class CommonPathsClassCollector extends CommonPathsFilesCollector
{
    protected const string CACHE_LOCATION = '/tmp/laravel_cache/%s_classes.php';

    protected string $cacheArrayLineFormat = '%s::class';

    public function collect(): Collection
    {
        return parent::collect()->map(static fn ($file) => pathToNamespace($file));
    }
}
