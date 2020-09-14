<?php

namespace ArturDoruch\ListBundle\Paginator;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
class MongoDBPaginator implements PaginatorInterface
{
    /**
     * @var \MongoCursor
     */
    private $cursor;

    /**
     * @var int
     */
    private $totalItems;

    public static function supportsQuery($query): bool
    {
        return $query instanceof \MongoCursor;
    }


    public function __construct($cursor, array $options = [])
    {
        $this->cursor = $cursor;
        $this->totalItems = $this->cursor->count();
    }


    public function getTotalItems(): int
    {
        return $this->totalItems;
    }


    public function getItems(int $offset, int $limit): \ArrayIterator
    {
        $this->cursor->skip($offset);

        if ($limit) {
            $this->cursor->limit($limit);
        }

        return new \ArrayIterator(iterator_to_array($this->cursor));
    }
}
 