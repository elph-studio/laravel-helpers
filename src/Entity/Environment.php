<?php

declare(strict_types=1);

namespace Elph\LaravelHelpers\Entity;

class Environment
{
    public const string LOCAL = 'local'; // development
    public const string TESTING = 'testing'; // PHP Unit || GitHub Workflow
    public const string STAGING = 'staging';
    public const string SANDBOX = 'sandbox';
    public const string PRODUCTION = 'production';

    public static function get(): string
    {
        return config('app.env');
    }

    public static function isDevelopment(): bool
    {
        return collect([self::LOCAL, self::TESTING])->contains(config('app.env'));
    }

    public static function isServerSide(): bool
    {
        return collect([self::STAGING, self::SANDBOX, self::PRODUCTION])->contains(config('app.env'));
    }

    public static function isLocal(): bool
    {
        return config('app.env') === self::LOCAL;
    }

    public static function isTesting(): bool
    {
        return config('app.env') === self::TESTING || self::isGitHub();
    }

    public static function isStaging(): bool
    {
        return config('app.env') === self::STAGING;
    }

    public static function isSandbox(): bool
    {
        return config('app.env') === self::SANDBOX;
    }

    public static function isProduction(): bool
    {
        return config('app.env') === self::PRODUCTION;
    }

    public static function isGitHub(): bool
    {
        return env('HOME') === '/home/runner';
    }
}
