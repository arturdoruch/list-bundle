<?php

namespace ArturDoruch\ListBundle\Paginator;

use Doctrine\MongoDB\CursorInterface;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\Query\Query;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
class DoctrineMongoDBPaginator implements PaginatorInterface
{
    /**
     * @var Query
     */
    private $query;

    /**
     * @var CursorInterface
     */
    private $cursor;

    /**
     * @var int
     */
    private $totalItems;

    public static function supportsQuery($query): bool
    {
        return $query instanceof CursorInterface || $query instanceof Builder || $query instanceof Query;
    }

    /**
     * @param Builder|Query|CursorInterface $query
     * @param array $options
     */
    public function __construct($query, array $options = [])
    {
        if ($query instanceof CursorInterface) {
            $this->cursor = $query;
        } else {
            if ($query instanceof Builder) {
                $query = $query->getQuery();
            }

            if ($query->getType() !== Query::TYPE_FIND) {
                throw new \UnexpectedValueException('ODM query must be a FIND type query.');
            }

            $this->query = $query;
        }

        $this->totalItems = $this->getCursor()->count();
    }


    public function getTotalItems(): int
    {
        return $this->totalItems;
    }


    public function getItems(int $offset, int $limit): \ArrayIterator
    {
        $cursor = $this->getCursor();
        $cursor->skip($offset);

        if ($limit) {
            $cursor->limit($limit);
        }

        return new \ArrayIterator($cursor->toArray());
    }

    /**
     * @return CursorInterface
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    private function getCursor()
    {
        if (!$this->cursor) {
            $this->cursor = clone $this->query->execute();
        }

        return $this->cursor;
    }
}
 