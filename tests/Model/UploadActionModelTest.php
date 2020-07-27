<?php

namespace Model;

use App\Dto\CsvFileRequesetDto;
use App\Dto\CsvFileResponseDto;
use App\Model\UploadActionModel;
use PHPUnit\Framework\TestCase;

class UploadActionModelTest extends TestCase
{
    const TEST_FILE_PATH = '/var/uploads/test_05.txt';
    /**
     * @var UploadActionModel
     */
    private $model;
    /**
     * @var array
     */
    private array $configuration;
    /**
     * @var mixed
     */
    private $redis;

    protected function setUp(): void
    {
        $this->configuration = require getcwd().'/config/testConfiguration.php';
        /** @var UploadActionModel $model */
        $this->model = $this->configuration['UploadActionModel'];
    }

    public function testUpload()
    {
        file_put_contents(self::TEST_FILE_PATH, <<<CSV
1, asdf asdf lkj,asdf@google.com,eeee,50
2, asdf asdf lkj,asdf@google.com,eeee,
3, asdf asdf lkj,asdf@dsaf.cv,uah,25.2
4, asdf asdf lkj,asdf@dsaf.cv,uah,25.2
5, asdf asdf lkj,asdf@dsaf.cv,uah,25.2
CSV
        );
        $dto = new CsvFileRequesetDto([['name' => 'test', 'tmp_name' => self::TEST_FILE_PATH, 'error' => null]]);
        $response = $this->model->upload($dto);
        $this->assertInstanceOf(CsvFileResponseDto::class, $response);

        $result = json_encode($response);
        $this->assertIsString($result);
    }

    public function testUploadNoFileException()
    {
        $this->expectErrorMessage('no file, try again');
        $dto = new CsvFileRequesetDto([]);
        $this->model->upload($dto);
    }

    public function testUploadErrorFileException()
    {
        $this->expectErrorMessage('bad file given');
        $dto = new CsvFileRequesetDto([['error' => 'new error']]);
        $this->model->upload($dto);
    }
}
