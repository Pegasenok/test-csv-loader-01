<?php

namespace Worker;

use App\Command\CommandId;
use App\Command\CommandRegistry;
use App\Command\RedisCommandDeployer;
use App\Command\UploadCsvCommand;
use App\Worker\RedisCommandListener;
use PHPUnit\Framework\TestCase;
use Redis;

class RedisCommandListenerTest extends TestCase
{
    const TEST_FILE_NAME = 'first';
    const TEST_FILE_PATH = '/tmp/test_01.txt';

    /**
     * @var array
     */
    private $configuration;
    /**
     * @var Redis
     */
    private $redis;

    protected function setUp(): void
    {
        $this->configuration = require getcwd() . '/config/testConfiguration.php';
        /** @var \Redis $redis */
        $this->redis = $this->configuration['redis'];
        $this->redis->flushDB();
    }

    public function testConsume()
    {
        $listener = new RedisCommandListener($this->redis, new CommandRegistry());
        $listener->initWorkingState();
        $listener->consume();
        $this->assertTrue($listener->isProcessQueueEmpty());

        $deployer = new RedisCommandDeployer($this->redis);
        $command = (new CommandRegistry())->getCommandFromArray(
            [
                'id' => 'testId',
                'name' => UploadCsvCommand::UPLOAD_CSV_COMMAND_NAME,
                'payload' => ['files' => [self::TEST_FILE_NAME => self::TEST_FILE_PATH]]
            ]
        );
        file_put_contents(self::TEST_FILE_PATH, 'a,b,c');

        $commandId = $deployer->deployCommand($command);
        ob_start();
        $listener->consume();
        $result = ob_get_clean();
        $this->assertStringContainsString('started', $result);
        $this->assertStringContainsString('completed', $result);
        $this->assertStringContainsString($commandId->getId(), $result);
        $this->assertTrue($listener->isProcessQueueEmpty());
    }

    public function testFailedConsume()
    {
        $command = $this->createMock(UploadCsvCommand::class);
        $command->method('jsonSerialize')->willReturn([
            'id' => 'aaa1',
            'name' => 'uploadCsv',
            'payload' => ['files' => [self::TEST_FILE_NAME => self::TEST_FILE_PATH]]
        ]);
        $command->method('getCommandId')->willReturn(new CommandId('aaa1'));
        $command->method('execute')->willReturn(false);
        $commandRegistryMock = $this->createMock(CommandRegistry::class);
        $commandRegistryMock->method('getCommandFromArray')->willReturn($command);
        $deployer = new RedisCommandDeployer($this->redis);
        $commandId = $deployer->deployCommand($command);
        $listener = new RedisCommandListener($this->redis, $commandRegistryMock);
        $listener->initWorkingState();
        ob_start();
        $listener->consume();
        $result = ob_get_clean();
        $this->assertStringContainsString('started', $result);
        $this->assertStringContainsString('failed', $result);
    }

    public function testBusyCommandConsume()
    {
        $listener = new RedisCommandListener($this->redis, new CommandRegistry());
        $listener->initWorkingState();
        $listener->consume();
        $this->assertTrue($listener->isProcessQueueEmpty());

        $deployer = new RedisCommandDeployer($this->redis);
        $command = (new CommandRegistry())->getCommandFromArray(
            [
                'id' => 'testId',
                'name' => UploadCsvCommand::UPLOAD_CSV_COMMAND_NAME,
                'payload' => ['files' => [self::TEST_FILE_NAME => self::TEST_FILE_PATH]]
            ]
        );
        file_put_contents(self::TEST_FILE_PATH, 'a,b,c');

        $commandId = $deployer->deployCommand($command);
        $this->redis->set($commandId->getId(), RedisCommandDeployer::PROGRESS_STATUS);
        ob_start();
        $listener->consume();
        $result = ob_get_clean();
        $this->assertStringContainsString('already in work', $result);
    }

    public function testExceptionConsume()
    {
        $command = $this->createMock(UploadCsvCommand::class);
        $command->method('jsonSerialize')->willReturn([
            'id' => 'aaa1',
            'name' => 'uploadCsv',
            'payload' => ['files' => [self::TEST_FILE_NAME => self::TEST_FILE_PATH]]
        ]);
        $command->method('getCommandId')->willReturn(new CommandId('aaa1'));
        $command->method('execute')->willThrowException(new \Exception('mocked exception'));
        $commandRegistryMock = $this->createMock(CommandRegistry::class);
        $commandRegistryMock->method('getCommandFromArray')->willReturn($command);
        $deployer = new RedisCommandDeployer($this->redis);
        $commandId = $deployer->deployCommand($command);
        $listener = new RedisCommandListener($this->redis, $commandRegistryMock);
        $listener->initWorkingState();
        ob_start();
        $listener->consume();
        $result = ob_get_clean();
        $this->assertStringContainsString("Got exception", $result);
        $this->assertStringContainsString("mocked exception", $result);
    }

    public function testDispatchedKillConsume()
    {
        $listener = new RedisCommandListener($this->redis, new CommandRegistry());
        $listener->initWorkingState();
        posix_kill(posix_getpid(), SIGINT);
        ob_start();
        pcntl_signal_dispatch();
        $result = ob_get_clean();
        $this->assertStringContainsString("Exiting on demand", $result);
    }
}
