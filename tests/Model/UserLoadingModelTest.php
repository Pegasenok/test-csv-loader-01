<?php

namespace Model;

use App\Builder\UserBuilder;
use App\Database\DatabaseStorage;
use App\Fixture\CsvFileFixture;
use App\Parser\CsvFileParser;
use App\Model\UserLoadingModel;
use App\Repository\UserRepository;
use App\Validation\UserValidation;
use PHPUnit\Framework\TestCase;

class UserLoadingModelTest extends TestCase
{
    const TEST_FILE_PATH = '/tmp/test_03.csv';
    /**
     * @var DatabaseStorage
     */
    private DatabaseStorage $storage;

    public function setUp(): void
    {
        $this->storage = new DatabaseStorage($_ENV['DATABASE_URL']); // todo need separate database for tests
        /** @noinspection SqlWithoutWhere */
        $this->storage->getConnection()->exec('delete from users');
    }

    public function tearDown(): void
    {

    }

    /**
     * @group benchmark
     * @doesNotPerformAssertions
     */
    public function testUploadBigFile()
    {
        $generator = new CsvFileFixture();
        $generator->generate(self::TEST_FILE_PATH, 20000);
        $time = microtime(true);
        $model = new UserLoadingModel(
            new UserRepository($this->storage),
            new CsvFileParser(new UserBuilder(new UserValidation()))
        );
        $model->uploadFile(new \SplFileObject(self::TEST_FILE_PATH));
        $time = microtime(true) - $time;
        echo "Time elapsed: $time";
    }

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
            new UserRepository($this->storage),
            new CsvFileParser(new UserBuilder(new UserValidation()))
        );
        $model->uploadFile(new \SplFileObject(self::TEST_FILE_PATH));
        $this->assertTrue(true);
    }
}
