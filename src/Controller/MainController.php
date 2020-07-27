<?php

namespace App\Controller;

use App\Command\RedisCommandDeployer;
use App\Database\DatabaseStorage;
use App\Dto\CsvFileRequesetDto;
use App\Dto\CsvFileResponseDto;
use App\Exception\NotFoundException;
use App\Form\UploadFormBuilder;
use App\Model\SearchFormModel;
use App\Model\SearchModel;
use App\Model\UploadActionModel;
use App\Model\UploadFormModel;
use App\Repository\UserRepository;

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
        $searchModel = new SearchFormModel();
        return $model->getUploadFormHtml() . $searchModel->getSearchHtml();
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
     * @return string|void
     * @throws NotFoundException
     */
    public function search()
    {
        $query = $_GET['q'] ?? false;
        if (!$query) {
            throw new NotFoundException();
        }
        $searchModel = new SearchModel(
            new UserRepository(
                new DatabaseStorage($_ENV['DATABASE_URL'])
            )
        );
        return $searchModel->findByFioOrEmail($query);
    }

    /**
     * @param CsvFileResponseDto $dto
     * @return CsvFileResponseDto|string
     */
    private function chooseReturn(CsvFileResponseDto $dto)
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