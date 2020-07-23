<?php

namespace App\Controller;

use App\Command\RedisCommandDeployer;
use App\Dto\CsvFileRequesetDto;
use App\Form\UploadFormBuilder;
use App\Model\UploadActionModel;
use App\Model\UploadFormModel;

class MainController
{
    const UPLOAD_URL = 'upload';

    /**
     * @return string
     * @throws \Exception
     */
    public function index()
    {
        $model = new UploadFormModel(new UploadFormBuilder());
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

        $model = new UploadActionModel(new RedisCommandDeployer($redis));
        return $model->upload($dto);
    }
}