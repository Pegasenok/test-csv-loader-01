<?php


namespace App\Dto;


use App\Exception\BadFileException;

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
        if (empty($files)) {
            throw new BadFileException('no file, try again', 400);
        }
        foreach ($files as $file) {
            if ($file['error']) {
                throw new BadFileException('bad file given');
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

}