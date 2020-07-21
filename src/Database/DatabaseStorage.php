<?php


namespace App\Database;


class DatabaseStorage
{
    private \PDO $connection;

    /**
     * Connection constructor.
     * @param string $dsn
     */
    public function __construct(string $dsn)
    {
        $this->connection = new \PDO($dsn);
    }

    /**
     * @return \PDO
     */
    public function getConnection(): \PDO
    {
        return $this->connection;
    }

    /**
     * @param \PDO $connection
     */
    public function setConnection(\PDO $connection): void
    {
        $this->connection = $connection;
    }
}