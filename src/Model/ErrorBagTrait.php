<?php


namespace App\Model;


trait ErrorBagTrait
{
    private array $errors = [];

    protected function addError($message)
    {
        $this->errors[] = $message;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}