<?php


namespace App\Model;


use App\Entity\User;
use App\Exception\UserBatchInsertException;
use App\Parser\CsvFileParser;
use App\Repository\UserRepository;
use App\Util\ErrorBagTrait;

class UserLoadingModel
{
    use ErrorBagTrait;

    private UserRepository $userRepository;
    private CsvFileParser $parser;
    private int $batchSize = 1000;

    /**
     * UserLoadingModel constructor.
     * @param UserRepository $repository
     * @param CsvFileParser $csvFileParser
     */
    public function __construct(UserRepository $repository, CsvFileParser $csvFileParser)
    {
        $this->userRepository = $repository;
        $this->parser = $csvFileParser;
    }

    /**
     * @param \SplFileObject $file
     */
    public function uploadFile(\SplFileObject $file)
    {
        $this->userRepository->openUserInsertBatch();
        $i = 0;
        foreach ($this->parser->streamParseFile($file) as $entityHolder) {
            try {
                $user = $entityHolder->getEntity();
                if (!$user instanceof User) {
                    throw new UserBatchInsertException('Bad user.');
                }
                $this->userRepository->addToBatch($user);

                if (++$i >= $this->batchSize) {
                    $this->userRepository->commitBatch();
                    $this->userRepository->openUserInsertBatch();
                    $i = 0;
                }
            } catch (UserBatchInsertException $e) {
                $this->addError("Line {$entityHolder->getRowId()} - {$e->getMessage()}");
            }
        }
        try {
            $this->userRepository->commitBatch();
        } catch (UserBatchInsertException $e) {
            $this->addError("Last line - {$e->getMessage()}");
        }
//        var_dump($this->parser->getErrors());
    }

    /**
     * @todo strategy
     * @param \SplFileObject $file
     */
    public function uploadFile2(\SplFileObject $file)
    {
        $this->userRepository->openUserInsertStatement();
        $i = 0;
        foreach ($this->parser->streamParseFile($file) as $entityHolder) {
            try {
                $user = $entityHolder->getEntity();
                if (!$user instanceof User) {
                    throw new UserBatchInsertException('Bad user.');
                }
                $this->userRepository->executeUserInsertStatement($user);

                if (++$i >= $this->batchSize) {
                    $this->userRepository->commit();
                    $this->userRepository->openUserInsertStatement();
                    $i = 0;
                }
            } catch (UserBatchInsertException $e) {
                $this->addError("Line {$entityHolder->getRowId()} - {$e->getMessage()}");
            }
        }
        try {
            $this->userRepository->commit();
        } catch (UserBatchInsertException $e) {
            $this->addError("Last line - {$e->getMessage()}");
        }
//        var_dump($this->parser->getErrors());
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