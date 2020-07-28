<?php

namespace Fixture;

use App\Fixture\CsvFileFixture;
use PHPUnit\Framework\TestCase;

class CsvFileFixtureTest extends TestCase
{

    const TEST_FILE_PATH_1 = '/var/uploads/test_03.csv';
    const TEST_FILE_PATH_2 = '/var/uploads/test_04.csv';

    public function testGenerate()
    {
        $csvFileFixture = new CsvFileFixture();
        $csvFileFixture->setShift(500);
        $csvFileFixture->generate(self::TEST_FILE_PATH_1, 100);
        $csvFileFixture->generate(self::TEST_FILE_PATH_2, 100, 500);

        $this->assertTrue(is_readable(self::TEST_FILE_PATH_1));
        $fileObject = new \SplFileObject(self::TEST_FILE_PATH_1);
        $firstLine = $fileObject->fgetcsv();
        $this->assertIsArray($firstLine);
        $this->assertEquals($csvFileFixture->getShift() + 1, $firstLine[0]);
        $this->assertGreaterThan(2500, $fileObject->getSize());
        unlink(realpath(self::TEST_FILE_PATH_1));

        $this->assertTrue(is_readable(self::TEST_FILE_PATH_2));
        $fileObject = new \SplFileObject(self::TEST_FILE_PATH_2);
        $firstLine = $fileObject->fgetcsv();
        $this->assertIsArray($firstLine);
        $this->assertEquals($csvFileFixture->getShift() + 1, $firstLine[0]);
        $this->assertGreaterThan(2500, $fileObject->getSize());
        unlink(realpath(self::TEST_FILE_PATH_2));
    }
}
