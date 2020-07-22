<?php


namespace App\Model;


use App\Builder\UserBuilder;
use App\Dto\UserCsvHolder;

class ParseCsvFileModel
{
    use ErrorBagTrait;
    const EXPECTED_FIELD_COUNT = 5;
    private array $errors = [];

    private UserBuilder $userBuilder;

    /**
     * ParseCsvFileModel constructor.
     * @param UserBuilder $userBuilder
     */
    public function __construct(UserBuilder $userBuilder)
    {
        $this->userBuilder = $userBuilder;
    }

    /**
     * @param \SplFileObject $file
     * @return \Generator|UserCsvHolder[]
     */
    public function parseFile(\SplFileObject $file): \Generator
    {
        while (!$file->eof()) {
            if (($csvArray = $file->fgetcsv()) && !(count($csvArray) == self::EXPECTED_FIELD_COUNT)) {
                $this->addError(sprintf("broken line %s", $file->key()));
                continue;
            }

            try {
                $user = $this->userBuilder->generateUserFromArray($csvArray);
            } catch (\TypeError $e) {
                $this->addError(sprintf($e->getMessage()."in line %s", $file->key()));
                continue;
            }

            yield new UserCsvHolder($user, $file->key());
        }
    }
}