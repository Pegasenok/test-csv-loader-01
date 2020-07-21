<?php


namespace App\Model;


use App\Controller\Main;
use App\Form\FormBuilder;

class UploadCsvFormModel
{
    const LOADING_INPUT_NAME = 'loading_csv';
    /**
     * @var FormBuilder
     */
    private FormBuilder $formBuilder;

    /**
     * Main constructor.
     * @param FormBuilder $builder
     */
    public function __construct(
        FormBuilder $builder
    ) {
        $this->formBuilder = $builder;
    }

    public function getUploadFormHtml()
    {
        $this->formBuilder->setFormName(Main::UPLOAD_URL);
        $this->formBuilder->addInputFile(self::LOADING_INPUT_NAME, 'Csv file for upload: ');
        return "Hello. Please choose your csv file with the following fields: ID Пользователя,ФИО Пользователя,Email,Валюта,Сумма</br>" . $this->formBuilder->getHtml();
    }
}