<?php

namespace ArturDoruch\ListBundle\Request;

/**
 * Request query sort parameter helper.
 *
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
class QuerySortHelper
{
    /**
     * @var string Sorting items query parameter value format.
     */
    private static $format = '{field}:{direction}';
    private static $patten = '^(?P<field>.+):(?P<direction>.+)$';

    /*
     * @param string $format Sorting items query parameter value format, like: "{field}:{direction}".
     */
    /*public static function setFormat(string $format)
    {
        self::$patten = $format;
    }*/

    /**
     * @param string $field
     * @param string $direction
     *
     * @return string
     */
    public static function create(string $field, string $direction)
    {
        return str_replace([
            '{field}',
            '{direction}'
        ], [
            $field,
            $direction
        ], self::$format);
    }

    /**
     * @param string $value The sort query parameter value.
     *
     * @return array The sort data as array with key-value pair "field" => "direction".
     */
    public static function parse(string $value)
    {
        if (!preg_match('/'.self::$patten.'/', $value, $sort)) {
            return [];
        }

        return [$sort['field'] => $sort['direction']];
    }
}
