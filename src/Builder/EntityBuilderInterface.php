<?php


namespace App\Builder;


use App\Entity\EntityInterface;

interface EntityBuilderInterface
{
    /**
     * @param $array
     * @return EntityInterface
     */
    public function generateEntityFromSimpleArray($array): EntityInterface;

    public function getExpectedFieldCount(): int;
}