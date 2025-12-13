<?php

declare(strict_types=1);

namespace Test\Integration;

use Elph\LaravelHelpers\Entity\Environment;
use Elph\LaravelTesting\Test\TestCase\IntegrationTestCase;
use Illuminate\Support\Facades\Config;

class EnvironmentTest extends IntegrationTestCase
{
    public function testWillGetDefaultTestingEnvironment(): void
    {
        $this->assertEquals('testing', $this->app->environment());
        $this->assertEquals('testing', Environment::get());
        $this->assertTrue(Environment::isTesting());
        $this->assertTrue(Environment::isDevelopment());
    }

    public function testWillGetTestingEnvironment(): void
    {
        // Mocks
        Config::set('app.env', 'testing');

        // Asserts
        $this->assertEquals('testing', Environment::get());
        $this->assertTrue(Environment::isTesting());
        $this->assertTrue(Environment::isDevelopment());
    }

    public function testWillGetLocalEnvironment(): void
    {
        // Mocks
        Config::set('app.env', 'local');

        // Asserts
        $this->assertEquals('local', Environment::get());
        $this->assertTrue(Environment::isLocal());
        $this->assertTrue(Environment::isDevelopment());
    }

    public function testWillGetStagingEnvironment(): void
    {
        // Mocks
        Config::set('app.env', 'staging');

        // Asserts
        $this->assertEquals('staging', Environment::get());
        $this->assertTrue(Environment::isStaging());
        $this->assertTrue(Environment::isServerSide());
    }

    public function testWillGetProductionEnvironment(): void
    {
        // Mocks
        Config::set('app.env', 'production');

        // Asserts
        $this->assertEquals('production', Environment::get());
        $this->assertTrue(Environment::isProduction());
        $this->assertTrue(Environment::isServerSide());
    }
}
