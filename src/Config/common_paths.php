<?php

declare(strict_types=1);

/*
 * To use default common paths config in application, create config/common_paths.php with next source:

<?php

declare(strict_types=1);

return require '/app/vendor/elph-studio/laravel-helpers/src/Config/common_paths.php';

 */

return [

    'controllers' => [
        'app_api' => 'app/Http/ApiController',
        'app_web' => 'app/Http/WebController',
        'module_api' => 'Module/*/Http/ApiController',
        'module_web' => 'Module/*/Http/WebController',
        'elph_api' => 'vendor/elph-studio/*/src/Http/ApiController',
        'elph_web' => 'vendor/elph-studio/*/src/Http/WebController',
    ],

    'requests' => [
        'app' => 'app/Http/Request',
        'module' => 'Module/*/Http/Request',
        'elph' => 'vendor/elph-studio/*/src/Http/Request',
    ],

    'responses' => [
        'app' => 'app/Http/Response',
        'module' => 'Module/*/Http/Response',
        'elph' => 'vendor/elph-studio/*/src/Http/Response',
    ],

    'entities' => [
        'app' => 'app/Entity',
        'module' => 'Module/*/Entity',
        'integration' => 'Integration/*/Entity',
        'elph' => 'vendor/elph-studio/*/src/Entity',
    ],

    'seeders' => [
        'database' => 'database/seeders',
        'module' => 'Module/*/Database/Seeder',
        'elph' => 'vendor/elph-studio/*/src/Seeder',
    ],

    'console_commands' => [
        'app' => 'app/Console',
        'module' => 'Module/*/Console',
        'integration' => 'Integration/*/Console',
        'elph' => 'vendor/elph-studio/*/src/Console',
    ],

    'schedule_commands' => [
        'app' => 'app/Schedule',
        'module' => 'Module/*/Schedule',
        'integration' => 'Integration/*/Schedule',
        'elph' => 'vendor/elph-studio/*/src/Schedule',
    ],

];
