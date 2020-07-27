<?php


namespace App\Util;


interface ErrorsAwareInterface
{
    /**
     * @return array
     */
    public function getErrors(): array;
}