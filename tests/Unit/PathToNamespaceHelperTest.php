<?php

declare(strict_types=1);

namespace Test\Unit;

use Elph\LaravelTesting\Test\TestCase\UnitTestCase;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;

class PathToNamespaceHelperTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Default library tests basepath is `/tmp`, so it need to be changed before running this test
        $this->app->setBasePath('/app');
    }

    #[DataProvider('dataForCorrectClassGeneration')]
    public function testWillGenerateClassFromPath(string $filePath, string $expectedClass): void
    {
        $this->assertSame($expectedClass, pathToNamespace($filePath));

        // Additionally test `/app/*` conversion without `App\*` in the classname beginning
        $this->assertSame($expectedClass, pathToNamespace($this->app->basePath($filePath)));
    }

    // phpcs:disable SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly.ReferenceViaFullyQualifiedName
    public static function dataForCorrectClassGeneration(): Generator
    {
        yield 'Local library level Class' => [
            'filePath' => 'src/Helper/NamespaceGenerator.php',
            'expectedClass' => \Elph\LaravelHelpers\Helper\NamespaceGenerator::class,
        ];

        yield 'Local test level Class' => [
            'filePath' => 'tests/Unit/PathToNamespaceHelperTest.php',
            'expectedClass' => \Test\Unit\PathToNamespaceHelperTest::class,
        ];

        yield 'External Elph library level Class' => [
            'filePath' => 'vendor/elph-studio/laravel-testing-tools/src/Test/TestCase/FeatureTestCase.php',
            'expectedClass' => \Elph\LaravelTesting\Test\TestCase\FeatureTestCase::class,
        ];

        yield 'External library level Class' => [
            'filePath' => 'vendor/friendsofphp/php-cs-fixer/src/Runner/Runner.php',
            'expectedClass' => \PhpCsFixer\Runner\Runner::class,
        ];
    }
    // phpcs:enable

    #[DataProvider('dataForWrongClassGeneration')]
    public function testTryToGenerateClassAndWillThrowRuntimeException(
        string $filePath,
        string $expectedExceptionMessage
    ): void {
        // Asserts
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage($expectedExceptionMessage);

        // Execution
        pathToNamespace($filePath);
    }

    public static function dataForWrongClassGeneration(): Generator
    {
        yield 'File not found' => [
            'filePath' => 'app/Non/Existing/File.php',
            'expectedExceptionMessage' => 'File "app/Non/Existing/File.php" not found.',
        ];

        yield 'File is not PHP' => [
            'filePath' => 'composer.json',
            'expectedExceptionMessage' => 'Can\'t generate class from "composer.json" file.',
        ];

        yield 'File is not a Class' => [
            'filePath' => 'src/Config/vendor_fixer.php',
            'expectedExceptionMessage' => 'Class "Elph\LaravelHelpers\Config\Vendor_fixer" not found.',
        ];
    }
}
