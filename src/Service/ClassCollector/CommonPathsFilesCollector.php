<?php

declare(strict_types=1);

namespace Elph\LaravelHelpers\Service\ClassCollector;

use Illuminate\Support\Collection;

class CommonPathsFilesCollector extends ClassCollector
{
    public function __construct(private readonly string $entity, private readonly string $suffix)
    {
    }

    protected function getEntity(): string
    {
        return $this->entity;
    }

    protected function collect(): Collection
    {
        $files = new Collection();
        collect(config('common_paths.' . $this->getEntity()))
            ->each(function ($path) use (&$files) {
                $files = $files->merge(
                    collectFiles($path, 1000, [$this->suffix])->toArray()
                );
            });

        return $files;
    }
}
