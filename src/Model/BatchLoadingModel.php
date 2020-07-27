<?php


namespace App\Model;


use App\Dto\EntityHolder;
use App\Entity\EntityInterface;
use App\Exception\BatchInsertException;
use App\Repository\RepositoryInterface;
use App\Util\ErrorBagTrait;
use App\Util\ErrorsAwareInterface;
use Traversable;

class BatchLoadingModel implements BatchLoadingInterface, ErrorsAwareInterface
{
    use ErrorBagTrait;

    private RepositoryInterface $repository;
    private array $batch;
    private int $batchSize = 1000;

    /**
     * BatchLoadingModel constructor.
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return RepositoryInterface
     */
    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }

    /**
     * @return array
     */
    public function getBatch(): array
    {
        return $this->batch;
    }

    /**
     * @param int $batchSize
     */
    public function setBatchSize(int $batchSize): void
    {
        $this->batchSize = $batchSize;
    }

    /**
     * @param Traversable|EntityHolder[] $entitiesWalker
     */
    public function batchLoadStream(Traversable $entitiesWalker)
    {
        $i = 0;
        $this->getRepository()->getConnection()->beginTransaction();
        foreach ($entitiesWalker as $entityHolder) {
            try {
                $this->addToBatch($entityHolder->getEntity());

                if (++$i >= $this->batchSize) {
                    $i = 0;
                    $this->getRepository()->insertBatch($this->getBatch());
                    $this->emptyBatch();
                    $this->repository->getConnection()->commit();
                    $this->repository->getConnection()->beginTransaction();
                }
            } catch (BatchInsertException $e) {
                $this->repository->getConnection()->rollback();
                $this->repository->getConnection()->beginTransaction();
                $this->addError("Line {$entityHolder->getRowId()} - {$e->getMessage()}");
            }
        }
        if (!$this->isBatchEmpty()) {
            try {
                $this->getRepository()->insertBatch($this->getBatch());
                $this->emptyBatch();
                $this->repository->getConnection()->commit();
            } catch (BatchInsertException $e) {
                $this->repository->getConnection()->rollback();
                $this->addError("Last batch - {$e->getMessage()}");
            }
        }
    }

    /**
     * @param EntityInterface $user
     */
    private function addToBatch(EntityInterface $user)
    {
        $this->batch[] = $user;
    }

    private function emptyBatch()
    {
        $this->batch = [];
    }

    private function isBatchEmpty()
    {
        return empty($this->batch);
    }

}