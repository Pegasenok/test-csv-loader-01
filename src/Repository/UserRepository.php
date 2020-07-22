<?php declare(strict_types = 1);


namespace App\Repository;


use App\Database\DatabaseStorage;
use App\Entity\User;
use App\Exception\UserBatchInsertException;
use PDOStatement;

class UserRepository
{
    private DatabaseStorage $storage;
    private PDOStatement $batch;

    /**
     * UserRepository constructor.
     * @param DatabaseStorage $storage
     */
    public function __construct(DatabaseStorage $storage)
    {
        $this->storage = $storage;
    }

    public function openUserInsertBatch()
    {
        $this->getConnection()->beginTransaction();
        $this->batch = $this->getConnection()->prepare(<<<SQL
insert into users (id, fio, email, currency, sum) values (?, ?, ?, ?, ?)
SQL
        );
    }

    /**
     * @param User $user
     * @throws UserBatchInsertException
     */
    public function addToBatch(User $user)
    {
        if (!$this->batch->execute([
            $user->getId(),
            $user->getFio(),
            $user->getEmail(),
            $user->getCurrency(),
            $user->getSum()
        ])) {
            $error = $this->batch->errorInfo();
            throw new UserBatchInsertException(sprintf("[%s]%s %s", $error[0], $error[1], $error[2]));
        };
    }

    /**
     * @return \PDO
     */
    public function getConnection(): \PDO
    {
        return $this->storage->getConnection();
    }

    public function commitBatch()
    {
        $this->getConnection()->commit();
    }
}