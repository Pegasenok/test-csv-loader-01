<?php

namespace Model;

use App\Builder\UserBuilder;
use App\Database\DatabaseStorage;
use App\Model\ParseCsvFileModel;
use App\Model\UserLoadingModel;
use App\Repository\UserRepository;
use App\Validation\UserValidation;
use PHPUnit\Framework\TestCase;

class UserLoadingModelTest extends TestCase
{
    const TEST_FILE_PATH = '/tmp/test_03.csv';

    public function testUploadFile()
    {
        file_put_contents(self::TEST_FILE_PATH, <<<CSV
2, asdf asdf lkj,asdf@dsaf.cv,uah,25.2
1, asdf asdf lkj,asdf@google.com,eeee,50
1, asdf asdf lkj,asdf@google.com,eeee,
2, asdf asdf lkj,asdf@dsaf.cv,uah,25.2
2, asdf asdf lkj,asdf@dsaf.cv,uah,25.2
1, asdf asdf lkj,asdf@google.com,uah,0.0
3, ' OR drop table users;,a@a.com,uah,0
CSV
        );
        $model = new UserLoadingModel(
            new UserRepository(new DatabaseStorage($_ENV['DATABASE_URL'])),
            new ParseCsvFileModel(new UserBuilder(new UserValidation()))
        );
        $model->uploadFile(new \SplFileObject(self::TEST_FILE_PATH));
        $this->assertTrue(true);
    }
}
