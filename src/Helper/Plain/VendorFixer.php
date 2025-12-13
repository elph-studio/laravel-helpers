<?php

declare(strict_types=1);

namespace App\Helper;

use JetBrains\PhpStorm\ArrayShape;
use RuntimeException;

/*
 * VendorFixer is plain PHP helper running without Laravel or any other libraries support.
 * It is used to rewrite small blocks of vendors libraries or any other text files that are auto-generated,
 * but needs to be modified before launching them in production.
 *
 * Default list of changes is specified in src/Config/vendor_fixer.php, and it can be overwritten or extended
 * in application by adding config/vendor_fixer.php.
 * Custom config can be also specified when running script:
 * vendor/elph-studio/laravel-helpers/src/Helper/Plain/VendorFixer.php --config=path/to/custom/config/vendor_fixer.php
 *
 * By default, if specified to overwrite file of line inside of it are not found, script will skip them.
 *
 * To run this script automatically, add these commands to compose.json of your project:
    "scripts": {
        "post-install-cmd": [
            "@php vendor/elph-studio/laravel-helpers/src/Helper/Plain/VendorFixer.php"
        ],
        "post-update-cmd": [
            "@php vendor/elph-studio/laravel-helpers/src/Helper/Plain/VendorFixer.php"
        ]
    }
 *
 * phpcs:disable Generic.PHP.ForbiddenFunctions
 * phpcs:disable Generic.Strings.UnnecessaryStringConcat.Found
 */
return new class () {
    private const array VENDOR_FIXER_CONFIG_LOCATIONS = [
        'config/vendor_fixer.php',
        'vendor/elph-studio/laravel-helpers/src/Config/vendor_fixer.php',
        'src/Config/vendor_fixer.php',
    ];

    public function __construct()
    {
        $this->replaceContent();
    }

    private function replaceContent(): void
    {
        foreach ($this->getReplaceContent() as $file => $replacements) {
            if (file_exists($file) === false) {
                continue;
            }

            $content = file_get_contents($file);
            $originalContent = $content;
            foreach ($replacements as $replacement) {
                $content = str_replace($replacement['from'], $replacement['to'], $content);
            }

            if ($content === $originalContent) {
                continue;
            }

            file_put_contents($file, $content);
        }
    }

    #[ArrayShape([
        '*' => [
            [
                'from' => 'string',
                'to' => 'string',
            ],
        ],
    ])]
    private function getReplaceContent(): array
    {
        $customConfig = $this->loadCustomConfig();
        if (is_array($customConfig) === true) {
            return $customConfig;
        }

        foreach (self::VENDOR_FIXER_CONFIG_LOCATIONS as $location) {
            if (file_exists($location) === false) {
                continue;
            }

            $config = require $location;

            return $config['replace_content'];
        }

        throw new RuntimeException('Could not find vendor_fixer.php config');
    }

    private function loadCustomConfig(): array|null
    {
        $options = getopt('', ['config:']);
        if (empty($options['config']) === true) {
            return null;
        }

        $location = explode(',', $options['config'])[0];
        if (file_exists($location) === false) {
            throw new RuntimeException('Could not find custom vendor_fixer.php config');
        }

        $config = require $location;

        return $config['replace_content'];
    }
};
