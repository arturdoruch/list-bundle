<?php

namespace ArturDoruch\ListBundle\Paginator;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
interface PaginatorInterface
{
    /**
     * Gets list total items.
     *
     * @return int
     */
    public function getTotalItems(): int;

    /**
     * Gets list items with offset and limit.
     *
     * @param int $offset
     * @param int $limit The items limit.
     *
     * @return \ArrayIterator
     */
    public function getItems(int $offset, int $limit): \ArrayIterator;
}
 