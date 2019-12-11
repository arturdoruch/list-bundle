<?php

namespace ArturDoruch\ListBundle\Request;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
class QueryParameterNames
{
    private static $names = [
        'page' => 'page',
        'limit' => 'limit',
        'sort' => 'sort'
    ];


    public static function setNames($page, $limit, $sort)
    {
        self::$names['page'] = $page;
        self::$names['limit'] = $limit;
        self::$names['sort'] = $sort;
    }

    /**
     * @return string
     */
    public static function getPage(): string
    {
        return self::$names['page'];
    }

    /**
     * @return string
     */
    public static function getLimit(): string
    {
        return self::$names['limit'];
    }

    /**
     * @return string
     */
    public static function getSort(): string
    {
        return self::$names['sort'];
    }

    /**
     * @return array
     */
    public static function all(): array
    {
        return self::$names;
    }
}
