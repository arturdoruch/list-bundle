<?php

namespace ArturDoruch\ListBundle\Paginator;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
interface PaginatorInterface
{
    /**
     * Checks if this paginator supports the given query.
     *
     * @param mixed $query An array, database query or cursor object.
     *
     * @return bool
     */
    public static function supportsQuery($query): bool;

    /**
     * @param mixed $query An array, database query or cursor object.
     * @param array $options The paginator options.
     */
    public function __construct($query, array $options = []);

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
 