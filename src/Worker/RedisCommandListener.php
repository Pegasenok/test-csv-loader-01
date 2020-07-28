<?php declare(ticks=1);


namespace App\Worker;


use App\Command\CommandRegistry;
use App\Command\RedisCommandDeployer;
use Redis;

class RedisCommandListener
{
    const MICRO_SECONDS = 500000;

    private Redis $redis;
    private bool $working;
    private CommandRegistry $registry;

    /**
     * CommandListener constructor.
     * @param Redis $redis
     * @param CommandRegistry $registry
     */
    public function __construct(Redis $redis, CommandRegistry $registry)
    {
        $this->redis = $redis;
        $this->registry = $registry;
    }

    public function consume(): void
    {
        if ($commandJson = $this->redis->rpoplpush(RedisCommandDeployer::QUEUE_NAME, $this->getWorkerProcessQueueName())) {
            try {
                $command = $this->registry->getCommandFromArray(
                    json_decode($commandJson, true)
                );
                $commandId = $command->getCommandId()->getId();
                echo "command $commandId started \n";
                if ($this->redis->getSet($commandId, RedisCommandDeployer::PROGRESS_STATUS) == RedisCommandDeployer::INITIAL_STATUS) {
                    if ($command->execute()) {
                        $this->redis->setex($commandId, RedisCommandDeployer::KEY_TTL, RedisCommandDeployer::COMPLETE_STATUS);
                        echo "command $commandId completed \n";
                    } else {
                        echo "command $commandId failed \n";
                    }
                } else {
                    echo "command $commandId already in work";
                }
                $this->redis->rPop($this->getWorkerProcessQueueName());
            } catch (\Exception $e) {
                echo "Got exception: " . $e->getMessage() . "\n";
            }
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public function listen()
    {
        $this->initWorkingState();

        while ($this->working) {
            $this->consume();
            $this->wait();
        }
        if (!$this->isProcessQueueEmpty()) {
            echo "I'm done!\n";
        } else {
            echo "Something is left behind! \n";
        }
    }

    public function initWorkingState(): void
    {
        $this->working = true;
        pcntl_signal(SIGINT, function ($signal) {
            if ($signal == SIGINT) {
                $this->working = false;
                echo "Exiting on demand\n";
            }
        });
    }

    /**
     * @return string
     */
    public function getWorkerProcessQueueName(): string
    {
        return sprintf("%s-p%s-%s", RedisCommandDeployer::QUEUE_NAME, gethostname(), getmypid());
    }

    /**
     * @codeCoverageIgnore
     */
    private function wait(): void
    {
        usleep(self::MICRO_SECONDS);
    }

    /**
     * @return bool
     */
    public function isProcessQueueEmpty(): bool
    {
        return count($this->redis->lRange($this->getWorkerProcessQueueName(), 0, 1)) == 0;
    }

}