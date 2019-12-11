<?php

namespace ArturDoruch\ListBundle;

use ArturDoruch\ListBundle\Paginator\PaginatorRegistry;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
class Paginator
{
    /**
     * @param mixed $query The array with items collection or paginator query (Doctrine ORM query, MongoDB ORM query, MongoCursor, ect.)
     * @param int $page
     * @param int $limit
     * @param array $options The paginator options. Look on the paginator classes implementing
     *                       ArturDoruch\ListBundle\Paginator\PaginatorInterface for available options.
     *
     * @return Pagination
     */
    public static function paginate($query, $page, $limit, array $options = [])
    {
        $paginator = PaginatorRegistry::get($query, $options);

        return new Pagination($paginator, $page, $limit);
    }
}
