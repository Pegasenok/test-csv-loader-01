<?php

namespace App\Controller;

use App\Command\RedisCommandDeployer;
use App\Dto\CsvFileRequesetDto;
use App\Form\FormBuilder;
use App\Model\UploadCsvFilesModel;
use App\Model\UploadCsvFormModel;

class Main
{
    const UPLOAD_URL = 'upload';

    /**
     * @return string
     * @throws \Exception
     */
    public function hello()
    {
        $model = new UploadCsvFormModel(new FormBuilder());
        return $model->getUploadFormHtml();
    }

    /**
     * @return \JsonSerializable
     * @throws \Exception
     */
    public function upload()
    {
        $dto = new CsvFileRequesetDto($_FILES);

        // todo inline redis initialization
        $redis = new \Redis();
        $redis->connect('redis');
        $redis->auth($_ENV['REDIS_PASS']);

        $model = new UploadCsvFilesModel(new RedisCommandDeployer($redis));
        return $model->upload($dto);
    }
}