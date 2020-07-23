<?php


namespace App\Dto;


class CsvFileRequesetDto
{
    private array $files;

    /**
     * CsvFileRequesetDto constructor.
     * @param $files $_FILES syntax
     * @throws \Exception
     */
    public function __construct($files)
    {
        foreach ($files as $file) {
            if (!$file['name']) {
                throw new \Exception('no file, try again', 400);
            }
            if ($file['error']) {
                throw new \Exception('bad file given');
            }
            $this->files[$file['name']] = new \SplFileObject($file['tmp_name']);
        }
    }

    /**
     * @return array|\SplFileObject[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @param array $files
     */
    public function setFiles(array $files): void
    {
        $this->files = $files;
    }

}