<?php


namespace App\Command;


class RedisCommandDeployer implements CommandDeployerInterface
{
    const QUEUE_NAME = 'q';
    const INITIAL_STATUS = 0;
    const PROGRESS_STATUS = 1;
    const COMPLETE_STATUS = 2;
    const KEY_TTL = 300;

    private \Redis $redis;

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
        $this->redis->lPush($this->getQueueName(), $serializedPayload);
        $this->redis->set($commandId->getId(), self::INITIAL_STATUS);
        [$lPushResult, $setResult] = $this->redis->exec();
        if ($lPushResult === false) {
            throw new CommandPoolException('error deploying command');
        }
        return $commandId;
    }

    /**
     * @return string
     */
    public function getQueueName(): string
    {
        return self::QUEUE_NAME;
    }
}