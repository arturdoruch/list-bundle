<?php

namespace ArturDoruch\ListBundle\Paginator;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
class DoctrinePaginator implements PaginatorInterface
{
    /**
     * @var Paginator
     */
    private $paginator;

    /**
     * @var int
     */
    private $totalItems;

    public static function supportsQuery($query): bool
    {
        return $query instanceof Query || $query instanceof QueryBuilder;
    }

    /**
     * @param Query|QueryBuilder $query A Doctrine ORM query or query builder.
     * @param array $options
     *  - fetchJoinCollection (bool) (default: false) Whether the query joins a collection.
     */
    public function __construct($query, array $options = [])
    {
        $this->paginator = new Paginator($query, $options['fetchJoinCollection'] ?? false);
        $this->totalItems = $this->paginator->count();
    }


    public function getTotalItems(): int
    {
        return $this->totalItems;
    }


    public function getItems(int $offset, int $limit): \ArrayIterator
    {
        $query = $this->paginator->getQuery();
        $query->setFirstResult($offset);

        if ($limit) {
            $query->setMaxResults($limit);
        }

        return $this->paginator->getIterator();
    }
}
 