<?php

declare(strict_types=1);

namespace Elph\LaravelHelpers\Helper;

use Illuminate\Support\Env as BaseEnv;
use Illuminate\Support\Str;

/**
 * In some cases, server variables are returned with new line at the end, so it must be removed manually.
 * This helper overrides original Laravel Illuminate\Support\Env and removes new line in the end of variable.
 *
 * To override original env, replace `vendor/laravel/framework/src/Illuminate/Support/helpers.php` line
 * use Illuminate\Support\Env; to use Elph\LaravelHelpers\Helper\EnvReader as Env;
 * This is not good practice, but it is what it is. You can do it by running src/Helper/Plain/VendorFixer.php
 *
 * Another way is to override original env is by modifying bootstrap/app.php and registering override before
 * Application launch. This is more correct way, you choose what way to do it.
 */
class EnvReader extends BaseEnv
{
    public static function get($key, $default = null)
    {
        $value = parent::get($key, $default);
        if (is_string($value) === false) {
            return $value;
        }

        return Str::replaceLast("\n", '', $value);
    }
}
