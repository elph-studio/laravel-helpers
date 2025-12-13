<?php

declare(strict_types=1);

namespace Elph\LaravelHelpers\Helper;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * This class generates Namespaces from path.
 * I.e. path/to/SomeFile.php => Path\To\SomeFile
 *
 * It fails to create namespaces for vendors directory, so required vendors should be described in
 * `app.custom_namespaces` config same way as CUSTOM_NAMESPACES constant. Constant and config will be merged.
 */
class NamespaceGenerator
{
    private const string COMPOSER_NAMESPACES_CACHE = '/tmp/laravel_cache/composer_namespaces.php';

    public function generateNamespaceFromPath(string $filePath): string
    {
        if (File::exists($filePath) === false && File::exists(base_path($filePath)) === false) {
            throw new RuntimeException(sprintf('File "%s" not found.', $filePath));
        }

        $filePathStr = Str::of($filePath)->replaceFirst(base_path() . DIRECTORY_SEPARATOR, '');
        $isDir = File::isDirectory($filePath) || File::isDirectory(base_path($filePath));
        if ($isDir === false && $filePathStr->endsWith('.php') === false) {
            throw new RuntimeException(sprintf('Can\'t generate class from "%s" file.', $filePath));
        }

        $namespace = '';

        $pathItems = $filePathStr
            ->replaceLast('.php', '')
            ->explode('/');

        $pathItems->each(static function ($item) use (&$namespace) {
            $itemStr = Str::of($item);
            if ($itemStr->isEmpty() === true) {
                return;
            }

            $namespace .= ($namespace !== '' ? '\\' : '') . $itemStr->ucfirst();
        });

        $this->updateNamespaces($namespace);

        if (class_exists($namespace) === false) {
            throw new RuntimeException(sprintf('Class "%s" not found.', $namespace));
        }

        return $namespace;
    }

    private function updateNamespaces(string &$namespace): void
    {
        $this
            ->getComposerNamespaces()
            ->merge(config('app.custom_namespaces', []))
            ->each(
                static function ($customNamespace, $customPath) use (&$namespace) {
                    $namespaceStr = Str::of($namespace);

                    $isCustomNamespace = $namespaceStr
                        ->replace('\\', '/')
                        ->lower()
                        ->startsWith($customPath);

                    if ($isCustomNamespace === false) {
                        return true;
                    }

                    $namespace = $customNamespace . $namespaceStr->substr(strlen($customPath))->toString();

                    return false;
                }
            );
    }

    private function getComposerNamespaces(): Collection
    {
        if (File::exists(self::COMPOSER_NAMESPACES_CACHE) === false) {
            $this->setupComposerNamespaces();
        }

        return collect(require self::COMPOSER_NAMESPACES_CACHE);
    }

    private function setupComposerNamespaces(): void
    {
        $packages = $this
            ->getLocalComposerNamespaces()
            ->merge($this->getPackagesNamespaces());

        $cacheDirectory = dirname(self::COMPOSER_NAMESPACES_CACHE);
        if (File::isDirectory($cacheDirectory) === false) {
            File::makeDirectory($cacheDirectory, 0755, true, true);
        }

        File::put(self::COMPOSER_NAMESPACES_CACHE, "<?php\n\nreturn " . var_export($packages->toArray(), true) . ";\n");
    }

    private function getLocalComposerNamespaces(): Collection
    {
        $content = json_decode(File::get('composer.json'), true, 512, JSON_THROW_ON_ERROR);

        $namespaces = collect();
        collect($content['autoload']['psr-4'] ?? [])
            ->merge($content['autoload-dev']['psr-4'] ?? [])
            ->each(function (string $path, string $namespace) use (&$namespaces) {
                $this->appendNamespaces($namespaces, $path, $namespace);
            });

        return $namespaces;
    }

    private function getPackagesNamespaces(): Collection
    {
        $namespaces = collect();

        collectDirectories('vendor', 2)
            ->filter(
                static fn (string $path) => Str::substrCount($path, '/') === 2
                    && File::exists($path . '/composer.json') === true
            )
            ->each(function (string $pathToPackage) use (&$namespaces) {
                $content = json_decode(File::get($pathToPackage . '/composer.json'), true, 512, JSON_THROW_ON_ERROR);

                collect($content['autoload']['psr-4'] ?? [])
                    ->each(function (array|string $path, string $namespace) use (&$namespaces, $pathToPackage) {
                        if (is_string($path) === true) {
                            $path = $pathToPackage . DIRECTORY_SEPARATOR . $path;
                            $this->appendNamespaces($namespaces, $path, $namespace);

                            return;
                        }

                        collect($path)->each(function (string $path) use (&$namespaces, $pathToPackage, $namespace) {
                            $path = $pathToPackage . DIRECTORY_SEPARATOR . $path;
                            $this->appendNamespaces($namespaces, $path, $namespace);
                        });
                    });
            });

        return $namespaces;
    }

    private function appendNamespaces(Collection $namespaces, string $path, string $namespace): void
    {
        $pathStr = Str::of($path);
        if ($pathStr->endsWith('/') === true) {
            $path = $pathStr->replaceLast('/', '')->toString();
        }

        $namespaceStr = Str::of($namespace);
        if ($namespaceStr->endsWith('\\') === true) {
            $namespace = $namespaceStr->replaceLast('\\', '')->toString();
        }

        $namespaces->put($path, $namespace);
    }
}
