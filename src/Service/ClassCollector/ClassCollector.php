<?php

declare(strict_types=1);

namespace Elph\LaravelHelpers\Service\ClassCollector;

use Elph\LaravelHelpers\Entity\Environment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

abstract class ClassCollector
{
    protected const string CACHE_LOCATION = '/tmp/laravel_cache/%s_files.php';

    protected bool $cacheWithKeys = false;
    protected string $cacheArrayLineFormat = "'%s'";

    abstract protected function getEntity(): string;
    abstract protected function collect(): Collection;

    public function get(): Collection
    {
        if (Environment::isServerSide() === false) {
            return $this->collect();
        }

        return $this->getFromCache();
    }

    protected function getFromCache(): Collection
    {
        $cacheFile = $this->getCacheLocation();
        if (File::exists($cacheFile)) {
            return collect(require $cacheFile);
        }

        $collection = $this->collect();
        $this->putToCache($collection, $cacheFile);

        return $collection;
    }

    protected function putToCache(Collection $collection, string $cacheFile): void
    {
        if ($collection->isEmpty()) {
            $data = "<?php\n\ndeclare(strict_types=1);\n\nreturn [\n];\n";
            File::put($cacheFile, $data);

            return;
        }

        $collection = $collection->map(fn ($line) => sprintf($this->cacheArrayLineFormat, $line));

        $rows = $this->cacheWithKeys === false ? $collection->implode(",\n\t") : '';
        $data = "<?php\n\ndeclare(strict_types=1);\n\nreturn [\n\t" . $rows . ",\n];\n";

        if ($this->cacheWithKeys === true) {
            $collection->each(static function ($value, $key) use (&$rows) {
                if (is_int($key)) {
                    $rows .= sprintf("\t%d => %s,\n", $key, $value);

                    return;
                }

                $rows .= sprintf("\t'%s' => %s,\n", $key, $value);
            });

            $data = "<?php\n\ndeclare(strict_types=1);\n\nreturn [\n" . $rows . "];\n";
        }

        $cacheDirectory = dirname($cacheFile);
        if (File::isDirectory($cacheDirectory) === false) {
            File::makeDirectory($cacheDirectory, 0755, true, true);
        }

        File::put($cacheFile, $data);
    }

    protected function getCacheLocation(): string|null
    {
        $folder = Str::of(static::CACHE_LOCATION)->beforeLast('/');
        $file = sprintf(static::CACHE_LOCATION, $this->getEntity());
        if (File::exists($folder->toString()) === true) {
            return sprintf(static::CACHE_LOCATION, $this->getEntity());
        }

        $folder = $folder->replaceFirst('./', '/app/');

        return File::exists($folder->toString()) === true
            ? Str::of($file)->replaceFirst('./', '/app/')->toString()
            : null;
    }
}
