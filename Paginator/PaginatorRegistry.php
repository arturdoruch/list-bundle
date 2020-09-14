<?php

namespace ArturDoruch\ListBundle\Paginator;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
class PaginatorRegistry
{
    /**
     * @var PaginatorInterface[]
     */
    private static $classes = [];

    /**
     * Registers paginator.
     *
     * @param string $paginatorClass The paginator class namespace. The paginator must implement
     *                               the ArturDoruch\ListBundle\Paginator\PaginatorInterface.
     */
    public static function add(string $paginatorClass)
    {
        self::validatePaginatorClass($paginatorClass);
        self::$classes[] = $paginatorClass;
    }

    /**
     * Gets a new instance of the paginator for given query.
     *
     * @param mixed $query
     * @param array $options The paginator options.
     *
     * @return PaginatorInterface
     */
    public static function get($query, array $options = [])
    {
        foreach (self::$classes as $paginatorClass) {
            if ($paginatorClass::supportsQuery($query)) {
                return new $paginatorClass($query, $options);
            }
        }

        throw new \RuntimeException(sprintf(
            'Paginator for "%s" query is not registered.', is_object($query) ? get_class($query) : gettype($query)
        ));
    }


    public static function validatePaginatorClass(string $paginatorClass)
    {
        try {
            $reflectionClass = new \ReflectionClass($paginatorClass);
        } catch (\ReflectionException $e) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid namespace "%s" of the paginator class.', $paginatorClass
            ));
        }

        if (!$reflectionClass->implementsInterface(PaginatorInterface::class)) {
            throw new \InvalidArgumentException(sprintf(
                'The paginator class "%s" must implement the "%s" interface.',
                $paginatorClass, PaginatorInterface::class
            ));
        }
    }
}
