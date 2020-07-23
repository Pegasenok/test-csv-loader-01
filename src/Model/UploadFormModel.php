<?php


namespace App\Model;


use App\Controller\MainController;
use App\Form\UploadFormBuilder;

class UploadFormModel
{
    const LOADING_INPUT_NAME = 'loading_csv';
    /**
     * @var UploadFormBuilder
     */
    private UploadFormBuilder $formBuilder;

    /**
     * Main constructor.
     * @param UploadFormBuilder $builder
     */
    public function __construct(
        UploadFormBuilder $builder
    ) {
        $this->formBuilder = $builder;
    }

    public function getUploadFormHtml()
    {
        $this->formBuilder->setFormName(MainController::UPLOAD_URL);
        $this->formBuilder->addInputFile(self::LOADING_INPUT_NAME, 'Csv file for upload: ');
        return "Hello. Please choose your csv file with the following fields: ID Пользователя,ФИО Пользователя,Email,Валюта,Сумма</br>" . $this->formBuilder->getHtml();
    }
}