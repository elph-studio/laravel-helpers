<?php

declare(strict_types=1);

use Elph\LaravelHelpers\Helper\NamespaceGenerator;
use Elph\LaravelHelpers\Helper\PathScanner;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

if (function_exists('namespaceToHumanReadable') === false) {
    function namespaceToHumanReadable(object|string $class): string
    {
        if (is_object($class) === true) {
            $class = $class::class;
        }

        /** @var string $className */
        $className = Str::of($class)->explode('\\')->last();

        return trim(implode(' ', preg_split('/(?=[A-Z])/', $className)));
    }
}

if (function_exists('pathToNamespace') === false) {
    function pathToNamespace(string $filePath): string
    {
        /** @var NamespaceGenerator $generator */
        $generator = app()->make(NamespaceGenerator::class);

        return $generator->generateNamespaceFromPath($filePath);
    }
}

if (function_exists('collectFiles') === false) {
    function collectFiles(string $path, int $maxDepth, array $suffixes = []): Collection
    {
        /** @var PathScanner $pathScanner */
        $pathScanner = app()->make(PathScanner::class);

        return $pathScanner->collectPathFiles($path, $maxDepth, $suffixes);
    }
}

if (function_exists('collectDirectories') === false) {
    function collectDirectories(string $path, int $maxDepth): Collection
    {
        /** @var PathScanner $pathScanner */
        $pathScanner = app()->make(PathScanner::class);

        return $pathScanner->collectDirectories($path, $maxDepth);
    }
}

if (function_exists('isJson') === false) {
    function isJson($string): bool
    {
        if (is_string($string) === false) {
            return false;
        }

        try {
            return is_array(json_decode($string, true, 512, JSON_THROW_ON_ERROR));
        } catch (JsonException) {
            return false;
        }
    }
}
