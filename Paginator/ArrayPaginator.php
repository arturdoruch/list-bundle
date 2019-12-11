<?php

namespace ArturDoruch\ListBundle\Paginator;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
class ArrayPaginator implements PaginatorInterface
{
    /**
     * @var array
     */
    private $array;

    /**
     * @var int
     */
    private $totalItems;

    public function __construct(array $array)
    {
        $this->array = $array;
        $this->totalItems = count($this->array);
    }


    public function getTotalItems(): int
    {
        return $this->totalItems;
    }


    public function getItems(int $offset, int $limit): \ArrayIterator
    {
        $limit = $limit ?: $this->totalItems;
        $items = array_slice($this->array, $offset, $limit);

        return new \ArrayIterator($items);
    }
}
 