<?php

namespace App\Utils;

use ArrayIterator;
use Doctrine\ORM\QueryBuilder;

class Paginator
{
    private const PAGE_SIZE = 20;
    private ArrayIterator $result;
    private int $numResult;
    private int $currentPage;

    public function __construct(
        private QueryBuilder $queryBuilder,
        private int $pageSaze = self::PAGE_SIZE,
    )
    {
    }

    final public function pagination(int $page): self
    {
        $this->currentPage = max(1 ,$page);
        $firstResult = (int) ($this->currentPage - 1) * $this->pageSaze;

        $query = $this->queryBuilder
            ->setFirstResult($firstResult)
            ->setMaxResults($this->pageSaze)
            ->getQuery();

        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($query,true);

        $this->result = $paginator->getIterator();
        $this->numResult = $paginator->count();
    }

    final function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    final function getResult(): ArrayIterator
    {
        return $this->result;
    }

    final public function getNumResult(): int
    {
        return $this->numResult;
    }

    final public function getLastPage(): int
    {
        return (int) ceil($this->numResult / $this->pageSaze);
    }

    /**
     * @return int
     */
    public function getPageSaze(): int
    {
        return $this->pageSaze;
    }
}