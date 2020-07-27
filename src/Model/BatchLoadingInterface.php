<?php


namespace App\Model;


use App\Dto\EntityHolder;
use Generator;

interface BatchLoadingInterface
{
    /**
     * @param Generator|EntityHolder[] $entitiesWalker
     */
    public function batchLoadStream(Generator $entitiesWalker);
}