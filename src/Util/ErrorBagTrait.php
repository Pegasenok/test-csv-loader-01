<?php


namespace App\Util;


trait ErrorBagTrait
{
    private array $errors = [];

    /**
     * @param string $message
     */
    protected function addError(string $message)
    {
        $this->errors[] = $message;
    }

    /**
     * @param array $errors
     */
    protected function addErrors(array $errors)
    {
        array_merge($this->errors, $errors);
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}