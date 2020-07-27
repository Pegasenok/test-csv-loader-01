<?php

namespace Command;

use App\Command\CommandPoolException;
use App\Command\CommandRegistry;
use App\Command\RedisCommandDeployer;
use App\Command\UploadCsvCommand;
use PHPUnit\Framework\TestCase;

class RedisCommandDeployerTest extends TestCase
{
    const TEST_DB_INDEX = 10;
    const TEST_FILE_NAME = 'one';
    const TEST_FILE_PATH = '/tmp/test_01.txt';

    private \Redis $redis;

    public function setUp (): void
    {
        $configuration = require getcwd().'/config/testConfiguration.php';
        $this->redis = $configuration['redis'];
        $this->redis->flushDB();
    }

    public function tearDown(): void
    {
        $this->redis->flushDB();
    }

    public function testDeployCommand()
    {
        $deployer = new RedisCommandDeployer($this->redis);
        $command = (new CommandRegistry())->getCommandFromArray(
            [
                'id' => 'testId',
                'name' => UploadCsvCommand::UPLOAD_CSV_COMMAND_NAME,
                'payload' => ['files' => [self::TEST_FILE_NAME => self::TEST_FILE_PATH]]
            ]
        );
        file_put_contents(self::TEST_FILE_PATH, 'a,b,c');
        $deployer->deployCommand($command);

        $commands = $this->redis->lRange($deployer->getQueueName(), 0, -1);
        $this->assertCount(1, $commands);
        $decodedCommand = json_decode($commands[0], true);
        $this->assertEquals(0, json_last_error());
        $this->assertArrayHasKey('id', $decodedCommand);
        $this->assertArrayHasKey('name', $decodedCommand);
        $this->assertArrayHasKey('payload', $decodedCommand);
        $this->assertEquals(self::TEST_FILE_PATH, $decodedCommand['payload']['files'][self::TEST_FILE_NAME] ?? false);
        $this->assertNotFalse($this->redis->get($decodedCommand['id']));
        $this->assertCount(2, $this->redis->keys('*'));
    }

    public function testDeployCommandException()
    {
        $redis = $this->createMock(\Redis::class);
        $redis->method('exec')->willReturn([false, true]);
        $this->expectException(CommandPoolException::class);
        $deployer = new RedisCommandDeployer($redis);
        $command = (new CommandRegistry())->getCommandFromArray(
            [
                'id' => 'testId',
                'name' => UploadCsvCommand::UPLOAD_CSV_COMMAND_NAME,
                'payload' => ['files' => [self::TEST_FILE_NAME => self::TEST_FILE_PATH]]
            ]
        );
        file_put_contents(self::TEST_FILE_PATH, 'a,b,c');
        $deployer->deployCommand($command);
    }
}
