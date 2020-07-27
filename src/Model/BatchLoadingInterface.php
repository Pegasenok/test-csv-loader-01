<?php


namespace App\Model;


use App\Dto\EntityHolder;
use Traversable;

interface BatchLoadingInterface
{
    /**
     * @param Traversable|EntityHolder[] $entitiesWalker
     */
    public function batchLoadStream(Traversable $entitiesWalker);

    /**
     * @param int $batchSize
     */
    public function setBatchSize(int $batchSize): void;
}