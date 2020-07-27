<?php

namespace App\Model;

use App\Exception\NotFoundException;

interface UserSearchModelInterface
{
    /**
     * @param string $query
     * @return string
     * @throws NotFoundException
     */
    public function findByFioOrEmail(string $query);
}