<?php

namespace Model;

use App\Builder\UserBuilder;
use App\Database\DatabaseStorage;
use App\Dto\EntityHolder;
use App\Entity\User;
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
    public function testUploadBigFileBenchmark(int $rows, int $batch, int $loadingStrategy = 1)
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

    /**
     * @testWith [500, 193]
     * [500, 50]
     * @param int $rows
     * @param int $batch
     * @param int $loadingStrategy
     */
    public function testUploadBigFile(int $rows, int $batch, int $loadingStrategy = 1)
    {
        $this->storage->getConnection()->exec('delete from users');
        $generator = new CsvFileFixture();
        $generator->generate(self::TEST_FILE_PATH, $rows);
        $model = new UserLoadingModel(
            new CsvFileParser(new UserBuilder(new UserValidation())),
            $this->getLoadingModelStrategy($loadingStrategy)
        );
        $model->setBatchSize($batch);
        $model->uploadFile(new \SplFileObject(self::TEST_FILE_PATH));
        unlink(realpath(self::TEST_FILE_PATH));
        $this->assertEquals(
            $rows,
            $this->storage->getConnection()->query('select count(1) from users')->fetch(\PDO::FETCH_COLUMN)
        );
    }

    public function testUploadEntityException()
    {
        $csvFileParser = $this->createMock(CsvFileParser::class);
        $csvFileParser
            ->method('streamParseFile')
            ->willReturn(new \ArrayIterator(array_fill(0, 3, new EntityHolder($this->getEmptyBrokenUser(), 1))));
        $model = new UserLoadingModel(
            $csvFileParser,
            $this->getLoadingModelStrategy(1)
        );
        $model->setBatchSize(2);
        $model->uploadFile($this->getMockBuilder(\SplFileObject::class)->setConstructorArgs(['php://memory'])->getMock());
        $this->assertContains('Line 1 - [22001]1406 Data too long for column \'currency\' at row 1', $model->getErrors());
        $this->assertContains('Last batch - [22001]1406 Data too long for column \'currency\' at row 1', $model->getErrors());

        $this->assertTrue(true);
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

    /**
     * @return User
     */
    private function getEmptyBrokenUser(): User
    {
        $user = new User();
        $user->setId(-1);
        $user->setFio('');
        $user->setEmail('');
        $user->setCurrency('aaaa');
        $user->setSum(0);
        return $user;
    }
}
