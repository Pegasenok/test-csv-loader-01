<?php

namespace App\Controller;

use App\Command\DummyCommandDeployer;
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
        $model = new UploadCsvFilesModel(new DummyCommandDeployer());
        return $model->upload($dto);
    }
}