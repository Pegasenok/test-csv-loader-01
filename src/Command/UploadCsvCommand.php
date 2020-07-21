<?php


namespace App\Command;


class UploadCsvCommand implements CommandInterface
{
    public string $name = 'uploadCsv';
    public array $payload;

    public function setFiles(array $files)
    {
        $this->payload = ['files' => $files];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}