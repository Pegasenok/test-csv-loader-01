<?php

namespace App\Controller;

use App\Command\RedisCommandDeployer;
use App\Dto\CsvFileRequesetDto;
use App\Dto\CsvFileResponseDto;
use App\Form\UploadFormBuilder;
use App\Model\UploadActionModel;
use App\Model\UploadFormModel;

class MainController
{
    const UPLOAD_URL = 'upload';
    const WAIT_STATUS_URL = 'status';

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
     * @return \JsonSerializable|string
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
        $dto = $model->upload($dto);
        return $this->chooseReturn($dto);
    }

    public function status()
    {
        // todo inline redis
        $redis = new \Redis();
        $redis->connect('redis');
        $redis->auth($_ENV['REDIS_PASS']);
        return json_encode([
            'status' => $redis->get($_GET['waitId'])
        ]);
    }

    /**
     * @param CsvFileResponseDto $dto
     * @return CsvFileResponseDto|string
     */
    public function chooseReturn(CsvFileResponseDto $dto)
    {
        $explode = explode(',', $_SERVER['HTTP_ACCEPT']);
        if (in_array('text/html', $explode)) {
            $model = new UploadFormModel(new UploadFormBuilder());
            return $model->getStatusFormHtml($dto->getWaitId());
        } else {
            return $dto;
        }
    }
}