<?php

declare(strict_types=1);

namespace Elph\LaravelHelpers\Helper;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Finder\SplFileInfo;

/**
 * PathScanner is an easy tool to scan any path directory and collect:
 * - All folders in specified directory
 * - All folders in specified directory and subdirectories
 * - All files in specified directory
 * - All files in specified directory and subdirectories
 * - Specific files by suffix in specified directory
 * - Specific files by suffix in specified directory and subdirectories
 */
class PathScanner
{
    public function collectDirectories(string $path, int $maxDepth): Collection
    {
        if ($maxDepth < 1) {
            throw new RuntimeException('Minimum Path Scanner depth must be greater than 0');
        }

        $directories = $this->createDirectoriesList($path);

        return $this->addSubDirectories($directories, 0, $maxDepth)->sort();
    }

    public function collectPathFiles(string $path, int $maxDepth = 10, array $suffixes = []): Collection
    {
        $files = new Collection();

        $this->collectDirectories($path, $maxDepth)
            ->each(static function (string $directory) use ($suffixes, &$files) {
                $scannedFiles = collect(File::files(base_path($directory)));

                if ($suffixes !== []) {
                    $scannedFiles = $scannedFiles->filter(
                        static fn (SplFileInfo $file) => Str::of($file->getBasename())->endsWith($suffixes) === true
                            && Str::of($file->getContents())->contains('abstract class') === false
                    );
                }

                $files = $files->merge($scannedFiles->map(
                    static fn (SplFileInfo $file) => Str::of($file->getPathname())
                        ->replaceFirst(base_path() . DIRECTORY_SEPARATOR, '')
                        ->toString()
                ));
            });

        return $files;
    }

    private function createDirectoriesList(string|null $path = null): Collection
    {
        $splitPath = Str::of($path)->explode(DIRECTORY_SEPARATOR)->filter();

        $tree = new Collection();

        $splitPath->each(function (string $directory) use (&$tree) {
            if ($directory === '*') {
                $tree = $this->collectAsterisk($tree);

                return;
            }

            if ($tree->isEmpty() === true && File::exists(base_path($directory)) === false) {
                return;
            }

            if ($tree->isEmpty() === true) {
                $tree->add($directory);

                return;
            }

            $tree->each(static function (string $path, string $key) use (&$tree, $directory) {
                $directoryPath = $path . DIRECTORY_SEPARATOR . $directory;
                if (File::exists(base_path($directoryPath)) === false) {
                    $tree->forget($key);

                    return;
                }

                $tree->offsetSet($key, $directoryPath);
            });
        });

        return $tree;
    }

    private function collectAsterisk(Collection $tree): Collection
    {
        $newTree = new Collection();
        $tree->each(static function (string $path) use (&$newTree) {
            $directories = collect(File::directories(base_path($path)));
            if ($directories->isEmpty() === true) {
                return;
            }

            $directories->each(static function (string $directory) use (&$newTree, $path) {
                $newTree->add(
                    $path .
                    DIRECTORY_SEPARATOR .
                    Str::of($directory)->explode(DIRECTORY_SEPARATOR)->last()
                );
            });
        });

        return $newTree;
    }

    private function addSubDirectories(Collection $directories, int $depth, int $maxDepth): Collection
    {
        $directories->each(function (string $directory) use (&$directories, $depth, $maxDepth) {
            if ($depth >= $maxDepth) {
                return;
            }

            $subDirectories = collect(File::directories(base_path($directory)))
                ->map(
                    static fn (string $subDirectory) => Str::of($subDirectory)
                        ->replaceFirst(base_path() . DIRECTORY_SEPARATOR, '')
                        ->toString()
                );

            $directories = $directories->merge($this->addSubDirectories($subDirectories, $depth + 1, $maxDepth));
        });

        return $directories;
    }
}
