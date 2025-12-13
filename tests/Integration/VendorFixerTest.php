<?php

declare(strict_types=1);

namespace Test\Integration;

use Elph\LaravelTesting\Test\TestCase\IntegrationTestCase;

class VendorFixerTest extends IntegrationTestCase
{
    private const string ORIGINAL_FILE = 'tests/Integration/VendorFixerTest.php';
    private const string TEMP_FILE = '/tmp/VendorFixerTest.php';

    public function testWillRunVendorFixerAndReplaceSeveralLinesInsideOfItself(): void
    {
        // Data
        copy(self::ORIGINAL_FILE, self::TEMP_FILE);

        // Assertion before execution to ensure files are identical
        $this->assertFileEquals(self::TEMP_FILE, self::ORIGINAL_FILE);

        // Execution
        exec('php src/Helper/Plain/VendorFixer.php --config=tests/Fixture/vendor_fixer_config.php', $output, $exitCode);

        // Assertion after execution to ensure execution is done and same files differs
        $this->assertEquals(0, $exitCode);
        $this->assertFileNotEquals(self::TEMP_FILE, self::ORIGINAL_FILE);
    }
}
