<?php


namespace App\Model;


use App\Entity\User;
use App\Exception\BatchInsertException;
use App\Repository\RepositoryInterface;
use App\Util\ErrorBagTrait;
use App\Util\ErrorsAwareInterface;

class SlowBatchLoadingModel implements BatchLoadingInterface, ErrorsAwareInterface
{
    use ErrorBagTrait;

    private RepositoryInterface $repository;
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
     * @inheritDoc
     */
    public function batchLoadStream(iterable $entitiesWalker)
    {
        $this->getRepository()->openUserInsertStatement();
        $i = 0;
        foreach ($entitiesWalker as $entityHolder) {
            try {
                $user = $entityHolder->getEntity();
                if (!$user instanceof User) {
                    throw new BatchInsertException('Bad user.');
                }
                $this->getRepository()->executeUserInsertStatement($user);

                if (++$i >= $this->batchSize) {
                    $i = 0;
                    $this->getRepository()->commit();
                    $this->getRepository()->openUserInsertStatement();
                }
            } catch (BatchInsertException $e) {
                $this->addError("Line {$entityHolder->getRowId()} - {$e->getMessage()}");
            }
        }
        $this->getRepository()->commit();
    }

    public function setBatchSize(int $batchSize): void
    {
        $this->batchSize = $batchSize;
    }
}