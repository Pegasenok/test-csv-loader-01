<?php


namespace App\Model;


use App\Exception\NotFoundException;
use App\Repository\UserRepository;

class SearchModel implements UserSearchModelInterface
{
    /**
     * @var UserRepository
     */
    private UserRepository$repository;

    /**
     * SearchModel constructor.
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string $query
     * @return string
     * @throws NotFoundException
     */
    public function findByFioOrEmail(string $query)
    {
        if (empty($query)) {
            throw new NotFoundException();
        }
        if ($email = filter_var($query, FILTER_VALIDATE_EMAIL)) {
            $results = $this->repository->findByEmail($query);
        } else {
            $results = $this->repository->findByFio($query);
        }
        if (empty($results)) {
            throw new NotFoundException();
        }
        return $this->buildHtmlFromResults($results);
    }

    /**
     * @param array $resultArray
     * @return string
     */
    private function buildHtmlFromResults(array $resultArray): string
    {
        $result = "<table>";
        foreach ($resultArray as $row) {
            $result .= "<tr>";
            $result .= $this->wrapCell($row['id']);
            $result .= $this->wrapCell($row['fio']);
            $result .= $this->wrapCell($row['email']);
            $result .= $this->wrapCell($row['currency']);
            $result .= $this->wrapCell($row['sum']);
            $result .= "</tr>";
        }
        $result .= "</table>";
        return $result;
    }

    /**
     * @param $row
     * @return mixed
     */
    private function wrapCell($row)
    {
        return "<td>" . $row . "</td>";
    }

}