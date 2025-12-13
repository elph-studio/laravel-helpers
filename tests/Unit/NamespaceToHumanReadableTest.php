<?php

declare(strict_types=1);

namespace Test\Unit;

use Elph\LaravelTesting\Test\TestCase\UnitTestCase;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;

// phpcs:disable SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly.ReferenceViaFullyQualifiedName
class NamespaceToHumanReadableTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Default library tests basepath is `/tmp`, so it need to be changed before running this test
        $this->app->setBasePath('/app');
    }

    #[DataProvider('dataForClassConversionFromClassToHumanReadableText')]
    public function testWillConvertClassToHumanReadableText(string $class, string $expected): void
    {
        $this->assertSame($expected, namespaceToHumanReadable($class));
    }

    public static function dataForClassConversionFromClassToHumanReadableText(): Generator
    {
        yield [
            'class' => \Test\Unit\NamespaceToHumanReadableTest::class,
            'expected' => 'Namespace To Human Readable Test',
        ];

        yield [
            'class' => \Elph\LaravelHelpers\Helper\NamespaceGenerator::class,
            'expected' => 'Namespace Generator',
        ];

        yield [
            'class' => \Elph\LaravelTesting\Test\TestCase\IntegrationTestCase::class,
            'expected' => 'Integration Test Case',
        ];

        yield [
            'class' => \Opay\OpaySniffs\Sniffs\Classes\NonExistingUsesSniff::class,
            'expected' => 'Non Existing Uses Sniff',
        ];
    }
}
