<?php

namespace ArturDoruch\ListBundle\Request;

/**
 * Helper class for creating and parsing URL query "sort" parameter of the
 * HTTP request getting list items.
 *
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
class QuerySort
{
    /**
     * @var string Sorting direction values.
     */
    private static $directions = [
        'asc' => 'asc',
        'desc' => 'desc',
    ];

    /**
     * @var array Positions of the sorting direction sign relative to the sorting field.
     */
    private static $directionPositions = ['before', 'after'];

    /**
     * @var string Position of the sorting direction sign relative to the sorting field.
     */
    private static $directionPosition = 'after';

    /**
     * @var string Separator between values of sorting direction and sorting field.
     */
    private static $directionSeparator = ':';

    /**
     * @var string
     */
    private static $parsingPattern = '/^(?P<field>.+):(?P<direction>(asc|desc))$/';

    /**
     * @param string $ascending The value for sorting ascending direction.
     * @param string $descending The value for sorting descending direction.
     * @param string $position Position of the sorting direction relative to the sorting field. One of the values: "before", "after".
     * @param string $separator Separator between sorting direction and sorting field.
     */
    public static function setDirectionConfig(string $ascending, string $descending, string $position, string $separator)
    {
        self::$directions = [
            'asc' => $ascending,
            'desc' => $descending,
        ];

        if (!in_array($position, self::$directionPositions)) {
            throw new \InvalidArgumentException(sprintf('Invalid direction position "%s".', $position));
        }

        self::$directionPosition = $position;

        if (preg_match('/[\?&=#]/', $separator)) {
            throw new \InvalidArgumentException(sprintf('Direction separator "%s" has not allowed characters "\?&=#".', $separator));
        }

        self::$directionSeparator = $separator;

        $fieldRegexp = '(?P<field>.+)';
        $directionRegexp = '(?P<direction>(' . preg_quote($ascending) . '|' . preg_quote($descending) . '))';
        self::$parsingPattern = '/^' .
            ($position === 'before'
                ? $directionRegexp . preg_quote($separator) . $fieldRegexp
                : $fieldRegexp . preg_quote($separator) . $directionRegexp
            ) . '$/';
    }

    /**
     * Creates query "sort" parameter.
     *
     * @param string $field
     * @param string $direction One of values: "asc", "desc".
     *
     * @return string
     */
    public static function create(string $field, string $direction): string
    {
        if ($direction !== 'asc' && $direction !== 'desc') {
            throw new \InvalidArgumentException(sprintf(
                'Invalid sort direction "%s". Allowed values are: "asc", "desc".', $direction
            ));
        }

        $direction = self::$directions[$direction];

        if (self::$directionPosition === 'after') {
            return $field . self::$directionSeparator . $direction;
        }

        return $direction . self::$directionSeparator . $field;
    }

    /**
     * Parses query "sort" parameter.
     *
     * @param string $value Query "sort" parameter.
     *
     * @return array An array with sorting properties, pair: "field" => "direction".
     */
    public static function parse(string $value): array
    {
        //$parts = explode(self::$directionSeparator, $value, 2);
        if (!preg_match(self::$parsingPattern, $value, $sort)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid query sort value "%s". The query do not match to the configured sort format.', $value
            ));
        }

        return [$sort['field'] => $sort['direction']];

        /*if (!isset($parts[1])) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid query sort value "%s". The query do not match to the configured sort format.', $value
            ));
        }

        $field = $parts[0];
        $direction = $parts[1];

        if (self::$directionPosition === 'before') {
            $field = $parts[1];
            $direction = $parts[0];
        }

        if (!in_array($direction, self::$directions)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid direction "%s" of the query sort value "%s". The query do not match to the configured sort format.',
                $direction, $value
            ));
        }

        return [$field => $direction];*/
    }

    /**
     * @param string $formattedDirection
     *
     * @return string
     */
    public static function getOppositeDirection(string $formattedDirection): string
    {
        return $formattedDirection === self::$directions['asc'] ? 'desc' : 'asc';
    }


    public static function getDirectionPositions(): array
    {
        return self::$directionPositions;
    }
}
