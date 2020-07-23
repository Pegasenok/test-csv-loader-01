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

    const USER_INSERT_BATCH_SIZE = 50;
    private UserRepository $userRepository;
    private CsvFileParser $parser;

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
            } catch (UserBatchInsertException $e) {
                $this->addError("Line {$entityHolder->getRowId()} - {$e->getMessage()}");
            }

            if (++$i > self::USER_INSERT_BATCH_SIZE) {
                $this->userRepository->commitBatch();
                $this->userRepository->openUserInsertBatch();
                $i = 0;
            }
        }
        $this->userRepository->commitBatch();
    }

}