<?php

namespace ArturDoruch\ListBundle\Paginator;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
abstract class AbstractPaginator implements PaginatorInterface
{

    final public function paginate(int $page, int $limit): \ArrayIterator
    {
        return $this->getItems(($page - 1) * $limit, $limit);
    }

    /**
     * Gets list items.
     *
     * @param int $offset
     * @param int $limit
     *
     * @return \ArrayIterator
     */
    abstract protected function getItems(int $offset, int $limit): \ArrayIterator;
}
