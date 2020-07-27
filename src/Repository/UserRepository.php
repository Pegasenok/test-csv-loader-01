<?php declare(strict_types=1);


namespace App\Repository;


use App\Builder\UserBuilder;
use App\Database\DatabaseStorage;
use App\Entity\User;
use App\Exception\UserBatchInsertException;
use PDO;

class UserRepository
{
    private DatabaseStorage $storage;
    private array $batch;
    /**
     * @var bool|\PDOStatement
     */
    private $insertBatchStatement;

    /**
     * UserRepository constructor.
     * @param DatabaseStorage $storage
     */
    public function __construct(DatabaseStorage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param string $query
     * @param int $limit
     * @return array
     */
    public function findByFio(string $query, int $limit = 10)
    {
        $statement = $this->getConnection()->prepare(<<<SQL
select id, fio, email, currency, sum from users where fio like ? LIMIT $limit
SQL
        );
        $statement->execute([$query . '%']);
        if ($statement->rowCount()) {
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        return [];
    }

    /**
     * @param string $query
     * @param int $limit
     * @return array
     */
    public function findByEmail(string $query, int $limit = 10)
    {
        $statement = $this->getConnection()->prepare(<<<SQL
select id, fio, email, currency, sum from users where email like ? LIMIT $limit
SQL
        );
        $statement->execute([$query]);
        if ($statement->rowCount()) {
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        return [];
    }

    public function openUserInsertBatch()
    {
        $this->batch = [];
        $this->getConnection()->beginTransaction();
    }

    /**
     * @param User $user
     */
    public function addToBatch(User $user)
    {
        $this->batch[] = $user;
    }

    /**
     * @return \PDO
     */
    public function getConnection(): \PDO
    {
        return $this->storage->getConnection();
    }

    /**
     * @throws UserBatchInsertException
     */
    public function commitBatch()
    {
        $insertScript = <<<SQL
insert into users (id, fio, email, currency, sum) values 
SQL
        ;
        for ($i = 0; $i < count($this->batch); $i++) {
            $quoteMarks = implode(',', str_split(str_repeat('?', UserBuilder::EXPECTED_FIELD_COUNT)));
            $insertScript .= '(' . $quoteMarks . '),';
        }
        $insertScript = substr($insertScript , 0, -1);
        $insertStatement = $this->getConnection()->prepare($insertScript);
        $flatBatch = array_reduce($this->batch, function(array $carry, User $item) {
            return array_merge($carry, [
                $item->getId(),
                $item->getFio(),
                $item->getEmail(),
                $item->getCurrency(),
                $item->getSum()
            ]);
        }, []);
        if (!$insertStatement->execute($flatBatch)) {
            $error = $insertStatement->errorInfo();
            throw new UserBatchInsertException(sprintf("[%s]%s %s", $error[0], $error[1], $error[2]));
        }
        $this->getConnection()->commit();
    }

    public function openUserInsertStatement()
    {
        $this->getConnection()->beginTransaction();
        $this->insertBatchStatement = $this->getConnection()->prepare(<<<SQL
insert into users (id, fio, email, currency, sum) values (?, ?, ?, ?, ?)
SQL
        );
    }

    public function executeUserInsertStatement(User $user)
    {
        if (!$this->insertBatchStatement->execute([
            $user->getId(),
            $user->getFio(),
            $user->getEmail(),
            $user->getCurrency(),
            $user->getSum()
        ])) {
            $error = $this->insertBatchStatement->errorInfo();
            throw new UserBatchInsertException(sprintf("[%s]%s %s", $error[0], $error[1], $error[2]));
        };
    }

    public function commit()
    {
        $this->getConnection()->commit();
    }
}