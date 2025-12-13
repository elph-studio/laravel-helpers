<?php

declare(strict_types=1);

namespace Elph\LaravelHelpers\Service\ClassCollector;

use Illuminate\Support\Collection;

class CommonPathsFoldersCollector extends ClassCollector
{
    protected const string CACHE_LOCATION = '/tmp/laravel_cache/%s_folders.php';

    public function __construct(private readonly string $entity)
    {
    }

    protected function getEntity(): string
    {
        return $this->entity;
    }

    protected function collect(): Collection
    {
        $folders = new Collection();
        collect(config('common_paths.' . $this->getEntity()))
            ->each(static function ($path) use (&$folders) {
                $folders = $folders->merge(
                    collectDirectories($path, 1000)->toArray()
                );
            });

        return $folders;
    }
}
