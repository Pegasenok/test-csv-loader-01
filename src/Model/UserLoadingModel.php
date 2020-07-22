<?php


namespace App\Model;


use App\Exception\UserBatchInsertException;
use App\Repository\UserRepository;

class UserLoadingModel
{
    use ErrorBagTrait;
    const USER_INSERT_BATCH_SIZE = 50;
    private UserRepository $userRepository;
    private ParseCsvFileModel $parseModel;

    /**
     * UserLoadingModel constructor.
     * @param UserRepository $repository
     * @param ParseCsvFileModel $parseCsvFileModel
     */
    public function __construct(UserRepository $repository, ParseCsvFileModel $parseCsvFileModel)
    {
        $this->userRepository = $repository;
        $this->parseModel = $parseCsvFileModel;
    }

    /**
     * @param \SplFileObject $file
     */
    public function uploadFile(\SplFileObject $file)
    {
        $this->userRepository->openUserInsertBatch();
        $i = 0;
        foreach ($this->parseModel->parseFile($file) as $userCsvHolder) {
            try {
                $this->userRepository->addToBatch($userCsvHolder->getUser());
            } catch (UserBatchInsertException $e) {
                $this->addError("Line {$userCsvHolder->getRowId()} - {$e->getMessage()}");
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