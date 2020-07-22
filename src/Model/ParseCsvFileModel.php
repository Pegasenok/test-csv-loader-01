<?php


namespace App\Model;


use App\Repository\UserRepository;
use App\Validation\ValidationException;

class ParseCsvFileModel
{
    const EXPECTED_FIELD_COUNT = 5;
    private array $errors = [];

    public function parseFile(\SplFileObject $file)
    {
        while (!$file->eof()) {
            // todo create and validate User entity
            if (($csvArray = $file->fgetcsv()) && !(count($csvArray) == self::EXPECTED_FIELD_COUNT)) {
                continue;
            }

            try {
                $user = UserRepository::generateUserFromArray($csvArray);
            } catch (ValidationException $e) {
                $this->addError('Validation not passed.');
                continue;
            } catch (\TypeError $e) {
                $this->addError($e->getMessage());
                continue;
            }

            yield $user;
        }
    }

    protected function addError($message)
    {
        $this->errors[] = $message;
    }

    /**
     * @return bool
     */
    public function hasErrors() {
        return !empty($this->errors);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}