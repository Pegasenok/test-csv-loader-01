<?php declare(ticks=1);


namespace App\Worker;


use App\Builder\UserBuilder;
use App\Command\RedisCommandDeployer;
use App\Command\UploadCsvCommand;
use App\Database\DatabaseStorage;
use App\Model\UserLoadingModel;
use App\Parser\CsvFileParser;
use App\Repository\UserRepository;
use App\Validation\UserValidation;
use Redis;

class RedisCommandListener
{
    const MICRO_SECONDS = 500000;
    /**
     * @var Redis
     */
    private Redis $redis;
    private bool $working;

    /**
     * CommandListener constructor.
     * @param Redis $redis
     */
    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * todo
     */
    public function listen()
    {
        $this->initWorkingState();

        while ($this->working) {
            if ($commandJson = $this->redis->rpoplpush(RedisCommandDeployer::QUEUE_NAME, $this->getWorkerProcessQueueName())) {
                try {
                    $commandArray = json_decode($commandJson, true);
                    if ($commandArray['name'] == UploadCsvCommand::UPLOAD_CSV_COMMAND_NAME) {
                        $model = new UserLoadingModel(
                            new UserRepository(new DatabaseStorage($_ENV['DATABASE_URL'])),
                            new CsvFileParser(new UserBuilder(new UserValidation()))
                        );
                        $command = UploadCsvCommand::stuff($commandArray);
                        $commandId = $command->getCommandId()->getId();
                        if ($this->redis->getSet($commandId, RedisCommandDeployer::PROGRESS_STATUS) == RedisCommandDeployer::INITIAL_STATUS) {
                            if ($command->execute($model)) {
                                $this->redis->setex($commandId, RedisCommandDeployer::KEY_TTL, RedisCommandDeployer::COMPLETE_STATUS);
                            }
                            echo "command $commandId completed \n";
                        } else {
                            echo "command $commandId already in work";
                        }
                        $this->redis->rPop($this->getWorkerProcessQueueName());
                    }
                } catch (\Exception $e) {
                    echo $e->getMessage() . "\n";
                }
            }
            usleep(self::MICRO_SECONDS);
        }
        if (!count($this->redis->lRange($this->getWorkerProcessQueueName(), 0, 1))) {
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


}