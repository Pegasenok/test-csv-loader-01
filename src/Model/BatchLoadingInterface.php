<?php


namespace App\Model;


use App\Dto\EntityHolder;

interface BatchLoadingInterface
{
    /**
     * @param iterable|EntityHolder[] $entitiesWalker
     */
    public function batchLoadStream(iterable $entitiesWalker);

    /**
     * @param int $batchSize
     */
    public function setBatchSize(int $batchSize): void;
}