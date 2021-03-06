<?php

namespace Model;

use App\Dto\CsvFileResponseDto;
use App\Form\UploadFormBuilder;
use App\Model\SearchFormModel;
use App\Model\UploadFormModel;
use PHPUnit\Framework\TestCase;

class UploadFormModelTest extends TestCase
{

    public function testGetUploadFormHtml()
    {
        $model = new UploadFormModel(new UploadFormBuilder());
        $searchModel = new SearchFormModel();
        $uploadFormHtml = $model->getUploadFormHtml();
        $this->assertIsString($uploadFormHtml);
        $searchHtml = $searchModel->getSearchHtml();
        $this->assertIsString($searchHtml);
        $this->assertStringContainsStringIgnoringCase("form method=\"post\"", $uploadFormHtml);
        $this->assertStringContainsStringIgnoringCase("<input type=\"text\" name=\"q\" />", $searchHtml);
    }

    public function testGetStatusFormHtml()
    {
        $model = new UploadFormModel(new UploadFormBuilder());
        $dto = new CsvFileResponseDto(true, 'abc');
        $html = $model->getStatusFormHtml($dto->getWaitId());
        $this->assertIsString($html);
        $this->assertStringContainsString('abc', $html);
        $this->assertStringContainsString('Check your status here', $html);
    }
}
