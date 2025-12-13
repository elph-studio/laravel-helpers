<?php

declare(strict_types=1);

namespace Test\Unit;

use Elph\LaravelTesting\Test\TestCase\UnitTestCase;
use Generator;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;

class PathScannerHelperTest extends UnitTestCase
{
    private const string INITIAL_DIRECTORY = '/tmp/PathScannerHelperTest/';

    protected function setUp(): void
    {
        parent::setUp();

        File::deleteDirectory(self::INITIAL_DIRECTORY);
    }

    #[DataProvider('dataForDirectoriesCollectionTest')]
    public function testWillReadDirectoryStructure(string $scanPath, int $depth, array $expected): void
    {
        // Data
        $this->createDirectories();

        // Assert
        $this->assertEquals(
            $expected,
            collectDirectories($scanPath, $depth)->toArray()
        );
    }

    public static function dataForDirectoriesCollectionTest(): Generator
    {
        yield 'Unlimited depth level' => [
            'scanPath' => self::INITIAL_DIRECTORY,
            'depth' => 1000,
            'expected' => [
                'PathScannerHelperTest',
                'PathScannerHelperTest/test1',
                'PathScannerHelperTest/test2',
                'PathScannerHelperTest/test1/abc',
                'PathScannerHelperTest/test1/abc/123',
                'PathScannerHelperTest/test1/abc/def',
                'PathScannerHelperTest/test1/abc/ghj',
                'PathScannerHelperTest/test1/abc/123/level4',
                'PathScannerHelperTest/test1/abc/123/level4/abc',
                'PathScannerHelperTest/test1/abc/123/level4/level5',
                'PathScannerHelperTest/test1/abc/def/level4',
                'PathScannerHelperTest/test1/abc/def/level4/level5',
                'PathScannerHelperTest/test1/abc/ghj/level4',
                'PathScannerHelperTest/test1/abc/ghj/level4/level5',
                'PathScannerHelperTest/test2/abc',
                'PathScannerHelperTest/test2/abc/123',
            ],
        ];

        yield 'Depth level 5' => [
            'scanPath' => self::INITIAL_DIRECTORY,
            'depth' => 5,
            'expected' => [
                'PathScannerHelperTest',
                'PathScannerHelperTest/test1',
                'PathScannerHelperTest/test2',
                'PathScannerHelperTest/test1/abc',
                'PathScannerHelperTest/test1/abc/123',
                'PathScannerHelperTest/test1/abc/def',
                'PathScannerHelperTest/test1/abc/ghj',
                'PathScannerHelperTest/test1/abc/123/level4',
                'PathScannerHelperTest/test1/abc/123/level4/abc',
                'PathScannerHelperTest/test1/abc/123/level4/level5',
                'PathScannerHelperTest/test1/abc/def/level4',
                'PathScannerHelperTest/test1/abc/def/level4/level5',
                'PathScannerHelperTest/test1/abc/ghj/level4',
                'PathScannerHelperTest/test1/abc/ghj/level4/level5',
                'PathScannerHelperTest/test2/abc',
                'PathScannerHelperTest/test2/abc/123',
            ],
        ];

        yield 'Depth level 4' => [
            'scanPath' => self::INITIAL_DIRECTORY,
            'depth' => 4,
            'expected' => [
                'PathScannerHelperTest',
                'PathScannerHelperTest/test1',
                'PathScannerHelperTest/test2',
                'PathScannerHelperTest/test1/abc',
                'PathScannerHelperTest/test1/abc/123',
                'PathScannerHelperTest/test1/abc/def',
                'PathScannerHelperTest/test1/abc/ghj',
                'PathScannerHelperTest/test1/abc/123/level4',
                'PathScannerHelperTest/test1/abc/def/level4',
                'PathScannerHelperTest/test1/abc/ghj/level4',
                'PathScannerHelperTest/test2/abc',
                'PathScannerHelperTest/test2/abc/123',
            ],
        ];

        yield 'Depth level 3' => [
            'scanPath' => self::INITIAL_DIRECTORY,
            'depth' => 3,
            'expected' => [
                'PathScannerHelperTest',
                'PathScannerHelperTest/test1',
                'PathScannerHelperTest/test2',
                'PathScannerHelperTest/test1/abc',
                'PathScannerHelperTest/test1/abc/123',
                'PathScannerHelperTest/test1/abc/def',
                'PathScannerHelperTest/test1/abc/ghj',
                'PathScannerHelperTest/test2/abc',
                'PathScannerHelperTest/test2/abc/123',
            ],
        ];

        yield 'Depth level 2' => [
            'scanPath' => self::INITIAL_DIRECTORY,
            'depth' => 2,
            'expected' => [
                'PathScannerHelperTest',
                'PathScannerHelperTest/test1',
                'PathScannerHelperTest/test2',
                'PathScannerHelperTest/test1/abc',
                'PathScannerHelperTest/test2/abc',
            ],
        ];

        yield 'Depth level 1' => [
            'scanPath' => self::INITIAL_DIRECTORY,
            'depth' => 1,
            'expected' => [
                'PathScannerHelperTest',
                'PathScannerHelperTest/test1',
                'PathScannerHelperTest/test2',
            ],
        ];

        yield 'Sub Directory test1 + depth level 2' => [
            'scanPath' => self::INITIAL_DIRECTORY . 'test1',
            'depth' => 2,
            'expected' => [
                'PathScannerHelperTest/test1',
                'PathScannerHelperTest/test1/abc',
                'PathScannerHelperTest/test1/abc/123',
                'PathScannerHelperTest/test1/abc/def',
                'PathScannerHelperTest/test1/abc/ghj',
            ],
        ];

        yield 'Sub Directory test2 + depth level 2' => [
            'scanPath' => self::INITIAL_DIRECTORY . 'test2',
            'depth' => 2,
            'expected' => [
                'PathScannerHelperTest/test2',
                'PathScannerHelperTest/test2/abc',
                'PathScannerHelperTest/test2/abc/123',
            ],
        ];

        yield 'Sub Directory with asterisk + depth level 2' => [
            'scanPath' => self::INITIAL_DIRECTORY . '*',
            'depth' => 2,
            'expected' => [
                'PathScannerHelperTest/test1',
                'PathScannerHelperTest/test2',
                'PathScannerHelperTest/test1/abc',
                'PathScannerHelperTest/test1/abc/123',
                'PathScannerHelperTest/test1/abc/def',
                'PathScannerHelperTest/test1/abc/ghj',
                'PathScannerHelperTest/test2/abc',
                'PathScannerHelperTest/test2/abc/123',
            ],
        ];

        yield 'Depth level 5 with several asterisks' => [
            'scanPath' => self::INITIAL_DIRECTORY . '*/abc/*/level4',
            'depth' => 5,
            'expected' => [
                'PathScannerHelperTest/test1/abc/123/level4',
                'PathScannerHelperTest/test1/abc/def/level4',
                'PathScannerHelperTest/test1/abc/ghj/level4',
                'PathScannerHelperTest/test1/abc/123/level4/abc',
                'PathScannerHelperTest/test1/abc/123/level4/level5',
                'PathScannerHelperTest/test1/abc/def/level4/level5',
                'PathScannerHelperTest/test1/abc/ghj/level4/level5',
            ],
        ];
    }

    public function testWillTryReadingNonExistingPathAndFindNothing(): void
    {
        // Assert
        $this->assertEquals(
            [],
            collectDirectories('/tmp/non/existing/directory', 10)->toArray()
        );
    }

    public function testWillTryPassingWrongDepthAndGetError(): void
    {
        // Assert
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Minimum Path Scanner depth must be greater than 0');

        // Execution
        collectDirectories('/tmp/non/existing/directory', 0);
    }

    #[DataProvider('dataForFilesCollectionTest')]
    public function testWillReadDirectoryFiles(string $scanPath, int $depth, array $suffixes, array $expected): void
    {
        // Data
        $this->createDirectories();
        $this->createFiles();

        // Assert
        $this->assertEquals(
            $expected,
            collectFiles($scanPath, $depth, $suffixes)->toArray()
        );
    }

    public static function dataForFilesCollectionTest(): Generator
    {
        yield 'Unlimited depth level & all suffixes' => [
            'scanPath' => self::INITIAL_DIRECTORY,
            'depth' => 1000,
            'suffixes' => [],
            'expected' => [
                'PathScannerHelperTest/init.json',
                'PathScannerHelperTest/init.php',
                'PathScannerHelperTest/init.txt',
                'PathScannerHelperTest/test1/abc/123/level4/test1_level4_file.json',
                'PathScannerHelperTest/test1/abc/123/level4/test1_level4_file.txt',
                'PathScannerHelperTest/test1/abc/123/level4/test1level4class.php',
                'PathScannerHelperTest/test1/abc/123/level4/level5/test1_level5_file1.json',
                'PathScannerHelperTest/test1/abc/123/level4/level5/test1_level5_file1.txt',
                'PathScannerHelperTest/test1/abc/123/level4/level5/test1_level5_file2.json',
                'PathScannerHelperTest/test1/abc/123/level4/level5/test1_level5_file2.txt',
                'PathScannerHelperTest/test1/abc/123/level4/level5/test1level5class1.php',
                'PathScannerHelperTest/test1/abc/123/level4/level5/test1level5class2.php',
                'PathScannerHelperTest/test2/abc/test.json',
                'PathScannerHelperTest/test2/abc/test.php',
                'PathScannerHelperTest/test2/abc/test.txt',
            ],
        ];

        yield 'Depth level 5 & only php+json' => [
            'scanPath' => self::INITIAL_DIRECTORY,
            'depth' => 5,
            'suffixes' => ['php', 'json'],
            'expected' => [
                'PathScannerHelperTest/init.json',
                'PathScannerHelperTest/init.php',
                'PathScannerHelperTest/test1/abc/123/level4/test1_level4_file.json',
                'PathScannerHelperTest/test1/abc/123/level4/test1level4class.php',
                'PathScannerHelperTest/test1/abc/123/level4/level5/test1_level5_file1.json',
                'PathScannerHelperTest/test1/abc/123/level4/level5/test1_level5_file2.json',
                'PathScannerHelperTest/test1/abc/123/level4/level5/test1level5class1.php',
                'PathScannerHelperTest/test1/abc/123/level4/level5/test1level5class2.php',
                'PathScannerHelperTest/test2/abc/test.json',
                'PathScannerHelperTest/test2/abc/test.php',
            ],
        ];

        yield 'Depth level 5 & only txt' => [
            'scanPath' => self::INITIAL_DIRECTORY,
            'depth' => 5,
            'suffixes' => ['txt'],
            'expected' => [
                'PathScannerHelperTest/init.txt',
                'PathScannerHelperTest/test1/abc/123/level4/test1_level4_file.txt',
                'PathScannerHelperTest/test1/abc/123/level4/level5/test1_level5_file1.txt',
                'PathScannerHelperTest/test1/abc/123/level4/level5/test1_level5_file2.txt',
                'PathScannerHelperTest/test2/abc/test.txt',
            ],
        ];

        yield 'Depth level 4 & only php+json' => [
            'scanPath' => self::INITIAL_DIRECTORY,
            'depth' => 4,
            'suffixes' => ['php', 'json'],
            'expected' => [
                'PathScannerHelperTest/init.json',
                'PathScannerHelperTest/init.php',
                'PathScannerHelperTest/test1/abc/123/level4/test1_level4_file.json',
                'PathScannerHelperTest/test1/abc/123/level4/test1level4class.php',
                'PathScannerHelperTest/test2/abc/test.json',
                'PathScannerHelperTest/test2/abc/test.php',
            ],
        ];

        yield 'Depth level 3 & only php' => [
            'scanPath' => self::INITIAL_DIRECTORY,
            'depth' => 3,
            'suffixes' => ['php'],
            'expected' => [
                'PathScannerHelperTest/init.php',
                'PathScannerHelperTest/test2/abc/test.php',
            ],
        ];

        yield 'Depth level 2 & only php' => [
            'scanPath' => self::INITIAL_DIRECTORY,
            'depth' => 2,
            'suffixes' => ['php'],
            'expected' => [
                'PathScannerHelperTest/init.php',
                'PathScannerHelperTest/test2/abc/test.php',
            ],
        ];

        yield 'Depth level 1 & only php+txt' => [
            'scanPath' => self::INITIAL_DIRECTORY,
            'depth' => 1,
            'suffixes' => ['php', 'txt'],
            'expected' => [
                'PathScannerHelperTest/init.php',
                'PathScannerHelperTest/init.txt',
            ],
        ];
    }

    private function createDirectories(): void
    {
        File::makeDirectory(self::INITIAL_DIRECTORY . 'test1/abc/123/level4/level5', 0755, true, true);
        File::makeDirectory(self::INITIAL_DIRECTORY . 'test1/abc/123/level4/abc', 0755, true, true);
        File::makeDirectory(self::INITIAL_DIRECTORY . 'test1/abc/def/level4/level5', 0755, true, true);
        File::makeDirectory(self::INITIAL_DIRECTORY . 'test1/abc/ghj/level4/level5', 0755, true, true);
        File::makeDirectory(self::INITIAL_DIRECTORY . 'test2/abc/123', 0755, true, true);
    }

    private function createFiles(): void
    {
        File::put(self::INITIAL_DIRECTORY . 'test1/abc/123/level4/level5/test1_level5_file1.json', '');
        File::put(self::INITIAL_DIRECTORY . 'test1/abc/123/level4/level5/test1_level5_file2.json', '');
        File::put(self::INITIAL_DIRECTORY . 'test1/abc/123/level4/level5/test1_level5_file1.txt', '');
        File::put(self::INITIAL_DIRECTORY . 'test1/abc/123/level4/level5/test1_level5_file2.txt', '');
        File::put(self::INITIAL_DIRECTORY . 'test1/abc/123/level4/level5/test1level5class1.php', '');
        File::put(self::INITIAL_DIRECTORY . 'test1/abc/123/level4/level5/test1level5class2.php', '');

        File::put(self::INITIAL_DIRECTORY . 'test1/abc/123/level4/test1_level4_file.json', '');
        File::put(self::INITIAL_DIRECTORY . 'test1/abc/123/level4/test1_level4_file.txt', '');
        File::put(self::INITIAL_DIRECTORY . 'test1/abc/123/level4/test1level4class.php', '');

        File::put(self::INITIAL_DIRECTORY . 'init.json', '');
        File::put(self::INITIAL_DIRECTORY . 'init.txt', '');
        File::put(self::INITIAL_DIRECTORY . 'init.php', '');

        File::put(self::INITIAL_DIRECTORY . 'test2/abc/test.json', '');
        File::put(self::INITIAL_DIRECTORY . 'test2/abc/test.txt', '');
        File::put(self::INITIAL_DIRECTORY . 'test2/abc/test.php', '');
    }
}
