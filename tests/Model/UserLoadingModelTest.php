<?php

namespace Model;

use App\Builder\UserBuilder;
use App\Database\DatabaseStorage;
use App\Fixture\CsvFileFixture;
use App\Model\BatchLoadingModel;
use App\Model\SlowBatchLoadingModel;
use App\Parser\CsvFileParser;
use App\Model\UserLoadingModel;
use App\Repository\UserRepository;
use App\Validation\UserValidation;
use PHPUnit\Framework\TestCase;

class UserLoadingModelTest extends TestCase
{
    const TEST_FILE_PATH = '/var/uploads/test_03.csv';
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
     * @testWith [1000, 100, 1]
     * [1000, 100, 2]
     * [10000, 1000, 1]
     * [10000, 1000, 2]
     * [10000, 5000, 1]
     * [10000, 5000, 2]
     * @param int $rows
     * @param int $batch
     * @param int $loadingStrategy
     */
    public function testUploadBigFile(int $rows, int $batch, int $loadingStrategy = 1)
    {
        $this->storage->getConnection()->exec('delete from users');
        $generator = new CsvFileFixture();
        $generator->generate(self::TEST_FILE_PATH, $rows);
        $time = microtime(true);
        $model = new UserLoadingModel(
            new CsvFileParser(new UserBuilder(new UserValidation())),
            $this->getLoadingModelStrategy($loadingStrategy)
        );
        $model->setBatchSize($batch);
        $model->uploadFile(new \SplFileObject(self::TEST_FILE_PATH));
        $time = microtime(true) - $time;
        unlink(realpath(self::TEST_FILE_PATH));
        echo "$rows/$batch @ $loadingStrategy time elapsed: $time\n";
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
            new CsvFileParser(new UserBuilder(new UserValidation())), new BatchLoadingModel(new UserRepository($this->storage))
        );
        $model->uploadFile(new \SplFileObject(self::TEST_FILE_PATH));
        $this->assertTrue(true);
    }

    /**
     * @param int $loadingStrategy
     * @return BatchLoadingModel|SlowBatchLoadingModel
     */
    private function getLoadingModelStrategy(int $loadingStrategy)
    {
        $repository = new UserRepository($this->storage);
        return $loadingStrategy == 2 ? new SlowBatchLoadingModel($repository) : new BatchLoadingModel($repository);
    }
}
