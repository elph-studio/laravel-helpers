<?php

declare(strict_types=1);

return [
    'replace_content' => [
        '/tmp/VendorFixerTest.php' => [
            [
                'from' => 'use Elph\LaravelTesting\Test\TestCase\IntegrationTestCase;',
                'to' => 'use Elph\LaravelTesting\Test\TestCase\FunctionTestCase;',
            ],
            [
                'from' => 'class VendorFixerTest extends IntegrationTestCase',
                'to' => 'class VendorFixerTest extends FunctionTestCase',
            ],
        ],
    ],
];
