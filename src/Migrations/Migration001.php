<?php


namespace App\Migrations;


use App\Database\DatabaseStorage;

class Migration001
{
    /**
     * @var DatabaseStorage
     */
    private DatabaseStorage $connection;

    /**
     * Migration001 constructor.
     * @param DatabaseStorage $connection
     */
    public function __construct(DatabaseStorage $connection)
    {
        $this->connection = $connection;
    }

    public function up()
    {
        $result = $this->connection->getConnection()->exec(<<<SQL
create table users
(
	id int auto_increment,
	fio varchar(300) null,
	email varchar(300) null,
	currency char(3) null,
	sum decimal(12,2) default 0.00 null,
	constraint users_pk
		primary key (id)
);

create index users_email_index
	on users (email);

create index users_fio_index
	on users (fio);


SQL
        );
        $this->checkExecResult($result);
    }

    public function down()
    {
        $result = $this->connection->getConnection()->exec(<<<SQL
drop table users;
SQL
        );
        $this->checkExecResult($result);
    }

    /**
     * @param int $result
     * @throws \Exception
     */
    public function checkExecResult($result): void
    {
        if ($result === false) {
            throw new \Exception($this->connection->getConnection()->errorInfo()[2] ?? 'undetermined db error');
        }
    }
}