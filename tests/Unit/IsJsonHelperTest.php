<?php

declare(strict_types=1);

namespace Test\Unit;

use Carbon\Carbon;
use Elph\LaravelTesting\Test\TestCase\UnitTestCase;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;

class IsJsonHelperTest extends UnitTestCase
{
    #[DataProvider('dataWithCorrectJsonStrings')]
    public function testWillValidateStringAsJson(string $json): void
    {
        $this->assertTrue(isJson($json));
    }

    public static function dataWithCorrectJsonStrings(): Generator
    {
        yield [
            'json' => '{
  "status": "ok"
}',
        ];

        yield [
            'json' => '{"status": "ok"}',
        ];

        yield [
            'json' => '{
  "status": "ok",
  "code": 200
}',
        ];

        yield [
            'json' => '{"name":"example","enabled":true,"retry":3}',
        ];

        yield [
            'json' => '{
  "user": {
    "id": 42,
    "name": "Alice"
  }
}',
        ];

        yield [
            'json' => '{"tags": ["php", "laravel", "testing"]}',
        ];

        yield [
            'json' => '{
  "servers": [
    { "host": "app1.local", "port": 80 },
    { "host": "app2.local", "port": 443 }
  ]
}',
        ];

        yield [
            'json' => '{
  "project": {
    "meta": {
      "name": "scanner",
      "version": "1.0.0"
    }
  }
}',
        ];

        yield [
            'json' => '{
  "company": {
    "departments": [
      {
        "name": "engineering",
        "employees": {
          "count": 12,
          "remote": true
        }
      }
    ]
  }
}',
        ];

        yield [
            'json' => '{
  "config": {
    "cache": {
      "drivers": {
        "redis": {
          "host": "127.0.0.1",
          "port": 6379
        }
      }
    }
  }
}',
        ];

        yield [
            'json' => '{
  "application": {
    "name": "example-app",
    "environment": "production",
    "features": {
      "auth": {
        "enabled": true,
        "providers": ["google", "github"]
      },
      "logging": {
        "level": "info",
        "outputs": {
          "file": {
            "path": "/var/log/app.log",
            "rotate": true
          }
        }
      }
    }
  }
}',
        ];
    }

    #[DataProvider('dataWithNonJson')]
    public function testWillFailToValidateDataAsJson($data): void
    {
        $this->assertFalse(isJson($data));
    }

    public static function dataWithNonJson(): Generator
    {
        yield [
            'data' => '1',
        ];

        yield [
            'data' => 1,
        ];

        yield [
            'data' => ['foo' => 'bar'],
        ];

        yield [
            'data' => 'true',
        ];

        yield [
            'data' => true,
        ];

        yield [
            'data' => collect(['foo' => 'bar']),
        ];

        yield [
            'data' => '<urlset>
    <url>
        <loc>http://cto4sale.com/</loc>
        <lastmod>2025-11-26T17:51:32.000000Z</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
</urlset>',
        ];

        yield [
            'data' => Carbon::now(),
        ];

        yield [
            'data' => '',
        ];
    }
}
