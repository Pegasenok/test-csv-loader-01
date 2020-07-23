<?php


namespace App\Dto;


use App\Entity\EntityInterface;

class EntityHolder
{
    private EntityInterface $entity;
    private int $rowId;

    /**
     * UserCsvHolder constructor.
     * @param EntityInterface $user
     * @param int $rowId
     */
    public function __construct(EntityInterface $user, int $rowId)
    {
        $this->entity = $user;
        $this->rowId = $rowId;
    }

    /**
     * @return EntityInterface
     */
    public function getEntity(): EntityInterface
    {
        return $this->entity;
    }

    /**
     * @param EntityInterface $entity
     */
    public function setEntity(EntityInterface $entity): void
    {
        $this->entity = $entity;
    }

    /**
     * @return int
     */
    public function getRowId(): int
    {
        return $this->rowId;
    }

    /**
     * @param int $rowId
     */
    public function setRowId(int $rowId): void
    {
        $this->rowId = $rowId;
    }
}