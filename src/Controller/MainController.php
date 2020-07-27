<?php

namespace App\Controller;

use App\Dto\CsvFileRequesetDto;
use App\Dto\CsvFileResponseDto;
use App\Exception\NotFoundException;
use App\Form\UploadFormBuilder;
use App\Model\CachedSearchModel;
use App\Model\SearchFormModel;
use App\Model\UploadActionModel;
use App\Model\UploadFormModel;
use App\Util\ConfigurationAwareTrait;
use Redis;

class MainController
{
    use ConfigurationAwareTrait;

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
        /** @var UploadActionModel $model */
        $model = $this->get('UploadActionModel');
        $dto = $model->upload($dto);
        return $this->chooseReturn($dto);
    }

    public function status()
    {
        /** @var Redis $redis */
        $redis = $this->get('redis');
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
        $cachedModel = new CachedSearchModel($this->get('SearchModel'), $this->get('cache'));
        return $cachedModel->findByFioOrEmail($query);
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