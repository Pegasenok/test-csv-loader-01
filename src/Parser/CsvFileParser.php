<?php


namespace App\Parser;


use App\Builder\EntityBuilderInterface;
use App\Dto\EntityHolder;
use App\Util\ErrorBagTrait;

class CsvFileParser
{
    use ErrorBagTrait;
    private array $errors = [];

    private EntityBuilderInterface $entityBuilder;

    /**
     * ParseCsvFileModel constructor.
     * @param EntityBuilderInterface $userBuilder
     */
    public function __construct(EntityBuilderInterface $userBuilder)
    {
        $this->entityBuilder = $userBuilder;
    }

    /**
     * @param \SplFileObject $file
     * @return \Generator|EntityHolder[]
     */
    public function streamParseFile(\SplFileObject $file): \Generator
    {
        while (!$file->eof()) {
            if (($csvArray = $file->fgetcsv()) && !(count($csvArray) == $this->entityBuilder->getExpectedFieldCount())) {
                $this->addError(sprintf("broken line %s", $file->key()));
                continue;
            }

            try {
                $entity = $this->entityBuilder->generateEntityFromSimpleArray($csvArray);
            } catch (\TypeError $e) {
                $this->addError(sprintf($e->getMessage()."in line %s", $file->key()));
                continue;
            }

            yield new EntityHolder($entity, $file->key());
        }
    }
}