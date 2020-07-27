<?php


namespace App\Model;


use App\Parser\FileParserInterface;
use App\Util\ErrorBagTrait;
use App\Util\ErrorsAwareInterface;

class UserLoadingModel implements ErrorsAwareInterface
{
    use ErrorBagTrait;

    private FileParserInterface $parser;
    private BatchLoadingInterface $batchLoadingModel;
    private int $batchSize = 1000;

    /**
     * UserLoadingModel constructor.
     * @param FileParserInterface $csvFileParser
     * @param BatchLoadingInterface $batchLoadingModel
     */
    public function __construct(
        FileParserInterface $csvFileParser,
        BatchLoadingInterface $batchLoadingModel
    )
    {
        $this->parser = $csvFileParser;
        $this->batchLoadingModel = $batchLoadingModel;
    }

    /**
     * @param \SplFileObject $file
     */
    public function uploadFile(\SplFileObject $file)
    {
        $this->batchLoadingModel->batchLoadStream(
            $this->parser->streamParseFile($file)
        );
        if ($this->parser instanceof ErrorsAwareInterface) {
            $this->addErrors($this->parser->getErrors());
        }
        if ($this->batchLoadingModel instanceof ErrorsAwareInterface) {
            $this->addErrors($this->batchLoadingModel->getErrors());
        }
    }

    /**
     * @return int
     */
    public function getBatchSize(): int
    {
        return $this->batchSize;
    }

    /**
     * @param int $batchSize
     */
    public function setBatchSize(int $batchSize): void
    {
        $this->batchSize = $batchSize;
    }

}