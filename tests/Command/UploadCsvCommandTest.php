<?php

namespace Command;

use App\Command\UploadCsvCommand;
use App\Database\DatabaseStorage;
use App\Model\UserLoadingModel;
use PHPUnit\Framework\TestCase;

class UploadCsvCommandTest extends TestCase
{
    const TEST_FILE_PATH_1 = '/tmp/a.txt';
    const TEST_FILE_PATH_2 = '/tmp/b.txt';

    /**
     * @var array
     */
    private $configuration;

    public function setUp (): void
    {
        $this->configuration = require getcwd().'/config/testConfiguration.php';
        /** @var DatabaseStorage $database */
        $database = $this->configuration['database'];
        $database->getConnection()->exec('delete from users');
    }

    public function testExecute()
    {
        file_put_contents(self::TEST_FILE_PATH_1, <<<CSV
2, asdf asdf lkj,asdf@dsaf.cv,uah,25.2
CSV
        );
        file_put_contents(self::TEST_FILE_PATH_2, <<<CSV
2, asdf asdf lkj,asdf@dsaf.cv,uah,111
3, asdf asdf lkj,asdf@dsaf.cv,uah,zzz
CSV
        );

        $this->assertTrue(file_exists(self::TEST_FILE_PATH_1));
        $this->assertTrue(file_exists(self::TEST_FILE_PATH_2));

        $command = new UploadCsvCommand($this->configuration['UserLoadingModel']);
        $command->setFiles([
            'one' => self::TEST_FILE_PATH_1,
            'two' => self::TEST_FILE_PATH_2,
        ]);
        $this->assertTrue($command->execute());
        $this->assertIsArray($command->getPayload()['errors']);
        $this->assertContains('Bad sum in line 1', $command->getPayload()['errors']);

        $this->assertFalse(file_exists(self::TEST_FILE_PATH_1));
        $this->assertFalse(file_exists(self::TEST_FILE_PATH_2));
    }
}
