<?php


namespace App\Dto;


use App\Entity\User;

class UserCsvHolder
{
    private User $user;
    private int $rowId;

    /**
     * UserCsvHolder constructor.
     * @param User $user
     * @param int $rowId
     */
    public function __construct(User $user, int $rowId)
    {
        $this->user = $user;
        $this->rowId = $rowId;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
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