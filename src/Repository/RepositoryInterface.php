<?php


namespace App\Repository;


use App\Entity\EntityInterface;
use App\Exception\BatchInsertException;

interface RepositoryInterface
{
    public function commit();
    public function getConnection(): \PDO;
    /**
     * @param array|EntityInterface[] $batch
     * @throws BatchInsertException
     */
    public function insertBatch(array $batch);
}