<?php

namespace Model;

use App\Builder\UserBuilder;
use App\Database\DatabaseStorage;
use App\Exception\NotFoundException;
use App\Model\BatchLoadingModel;
use App\Model\SearchModel;
use App\Model\UserLoadingModel;
use App\Parser\CsvFileParser;
use App\Repository\UserRepository;
use App\Validation\UserValidation;
use PHPUnit\Framework\TestCase;

class SearchModelTest extends TestCase
{
    const TEST_FILE_PATH = "/tmp/test_04.csv";
    /**
     * @var DatabaseStorage
     */
    private DatabaseStorage $storage;

    public function setUp(): void
    {
        $this->storage = new DatabaseStorage($_ENV['DATABASE_URL']); // todo need separate database for tests
        /** @noinspection SqlWithoutWhere */
        $this->storage->getConnection()->exec('delete from users');
        file_put_contents(self::TEST_FILE_PATH, <<<CSV
1,asdf asdf lkj,asdf@google.com,eee,50
2,kkklasdrf,asdf@google.com,eee,30
3,ooo,asdf@dsaf.cv,uah,25.2
4,  ,asdf@dsaf.cv,uah,25.2
5,,asdf@dsaf.cv,uah,25.2
CSV
        );
        $model = new UserLoadingModel(
            new CsvFileParser(new UserBuilder(new UserValidation())),
            new BatchLoadingModel(new UserRepository($this->storage))
        );
        $model->uploadFile(new \SplFileObject(self::TEST_FILE_PATH));
    }

    public function tearDown(): void
    {

    }

    public function testFindByFioOrEmail()
    {
        $model = new SearchModel(
            new UserRepository(
                new DatabaseStorage($_ENV['DATABASE_URL'])
            )
        );

        try {
            $result = $model->findByFioOrEmail('kkklasdrf');
        } catch (NotFoundException $e) {
            $this->fail('query not found!');
        }
        $this->assertIsString($result);
        $this->assertIsString($model->findByFioOrEmail('  '));
        $this->assertIsString($model->findByFioOrEmail('asdf@dsaf.cv'));

    }

    /**
     * @param $query
     * @throws NotFoundException
     * @testWith ["zzz"]
     * ["bad@email.com"]
     * [""]
     */
    public function testFindByFioOrEmailNotFoundException(string $query)
    {
        $model = new SearchModel(
            new UserRepository(
                new DatabaseStorage($_ENV['DATABASE_URL'])
            )
        );

        $this->expectException(NotFoundException::class);
        $model->findByFioOrEmail($query);
    }
}
