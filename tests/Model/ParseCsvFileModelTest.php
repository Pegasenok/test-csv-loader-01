<?php declare(strict_types=1);

namespace Model;

use App\Builder\UserBuilder;
use App\Model\ParseCsvFileModel;
use App\Validation\UserValidation;
use PHPUnit\Framework\TestCase;

class ParseCsvFileModelTest extends TestCase
{
    const TEST_FILE_PATH = '/tmp/test_02.csv';

    public function testParseFile()
    {
        file_put_contents(self::TEST_FILE_PATH, <<<CSV
1, asdf asdf lkj,asdf@google.com,eeee,50
1, asdf asdf lkj,asdf@google.com,eeee,
2, asdf asdf lkj,asdf@dsaf.cv,uah,25.2
2, asdf asdf lkj,asdf@dsaf.cv,uah,25.2
2, asdf asdf lkj,asdf@dsaf.cv,uah,25.2
CSV
);
        $model = new ParseCsvFileModel(new UserBuilder(new UserValidation()));
        foreach ($model->parseFile(new \SplFileObject(self::TEST_FILE_PATH)) as $item) {
            $this->assertIsObject($item);
        }
        $this->assertCount(2, $model->getErrors());
        $this->assertTrue(true);
    }
}
