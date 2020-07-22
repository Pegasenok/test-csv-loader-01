<?php


namespace App\Command;


class RedisCommandDeployer implements CommandDeployerInterface
{
    private \Redis $redis;
    private string $queueName = 'q';

    /**
     * RedisCommandDeployer constructor.
     * @param \Redis $redis
     */
    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    public function deployCommand(CommandInterface $command): CommandId
    {
        $commandId = new CommandId(uniqid());
        $command->setCommandId($commandId);
        $serializedPayload = json_encode($command);
        $this->redis->multi();
        $this->redis->lPush($this->queueName, $serializedPayload);
        $this->redis->set($commandId->getId(), 0);
        [$lPushResult, $setResult] = $this->redis->exec();
        if ($lPushResult === false) {
            throw new CommandPoolException('error deploying command');
        }
        return $commandId;
    }

    public function getOneCommand(): CommandInterface
    {
        // TODO: Implement getOneCommand() method.
    }

    /**
     * @return string
     */
    public function getQueueName(): string
    {
        return $this->queueName;
    }
}