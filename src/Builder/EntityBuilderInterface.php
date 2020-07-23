<?php


namespace App\Builder;


use App\Entity\EntityInterface;
use App\Exception\UserFieldSetException;

interface EntityBuilderInterface
{
    /**
     * @param $array
     * @return EntityInterface
     * @throws UserFieldSetException
     */
    public function generateEntityFromSimpleArray($array): EntityInterface;

    public function getExpectedFieldCount(): int;
}