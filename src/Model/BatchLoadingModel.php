<?php


namespace App\Model;


use App\Dto\EntityHolder;
use App\Entity\EntityInterface;
use App\Exception\BatchInsertException;
use App\Repository\RepositoryInterface;
use App\Util\ErrorBagTrait;
use App\Util\ErrorsAwareInterface;
use Generator;

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
     * @param RepositoryInterface $repository
     */
    public function setRepository(RepositoryInterface $repository): void
    {
        $this->repository = $repository;
    }

    /**
     * @return array
     */
    public function getBatch(): array
    {
        return $this->batch;
    }

    /**
     * @param array $batch
     */
    public function setBatch(array $batch): void
    {
        $this->batch = $batch;
    }

    /**
     * @return int
     */
    public function getBatchSize(): int
    {
        return $this->batchSize;
    }

    /**
     * @param int $batchSize
     */
    public function setBatchSize(int $batchSize): void
    {
        $this->batchSize = $batchSize;
    }

    /**
     * @param Generator|EntityHolder[] $entitiesWalker
     */
    public function batchLoadStream(Generator $entitiesWalker)
    {
        $i = 0;
        $this->getRepository()->getConnection()->beginTransaction();
        foreach ($entitiesWalker as $entityHolder) {
            try {
                $entity = $entityHolder->getEntity();
                if (!$entity instanceof EntityInterface) {
                    throw new BatchInsertException('Bad entity.');
                }
                $this->addToBatch($entity);

                if (++$i >= $this->batchSize) {
                    $i = 0;
                    $this->getRepository()->insertBatch($this->getBatch());
                    $this->emptyBatch();
                    $this->repository->getConnection()->commit();
                    $this->repository->getConnection()->beginTransaction();
                }
            } catch (BatchInsertException $e) {
                $this->repository->getConnection()->rollback();
                $this->addError("Line {$entityHolder->getRowId()} - {$e->getMessage()}");
            }
        }
        if (!$this->isBatchEmpty()) {
            try {
                $this->getRepository()->insertBatch($this->getBatch());
                $this->emptyBatch();
                $this->repository->getConnection()->commit();
            } catch (BatchInsertException $e) {
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