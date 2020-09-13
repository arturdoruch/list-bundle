<?php

namespace ArturDoruch\ListBundle\Paginator;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
class PaginatorRegistry
{
    /**
     * @var array
     */
    private static $registry = [
        \Doctrine\ORM\Query::class => DoctrinePaginator::class,
        \Doctrine\ORM\QueryBuilder::class => DoctrinePaginator::class,
        \Doctrine\ODM\MongoDB\Query\Query::class => DoctrineMongoDBPaginator::class,
        \Doctrine\ODM\MongoDB\Query\Builder::class => DoctrineMongoDBPaginator::class,
        \Doctrine\MongoDB\CursorInterface::class => DoctrineMongoDBPaginator::class,
        \MongoCursor::class => MongoDBPaginator::class,
    ];

    /**
     * Registers paginator.
     *
     * @param string $queryClass
     * @param string $paginatorClass
     */
    public static function add(string $queryClass, string $paginatorClass)
    {
        self::validateQueryClass($queryClass);
        self::validatePaginatorClass($paginatorClass);

        self::$registry[$queryClass] = $paginatorClass;
    }

    /**
     * Detects and gets a new instance of the paginator.
     *
     * @param mixed $query
     * @param array $options
     *
     * @return PaginatorInterface
     */
    public static function get($query, array $options = [])
    {
        if (is_array($query)) {
            return new ArrayPaginator($query);
        }

        if (!is_object($query)) {
            throw new \InvalidArgumentException(sprintf(
                'The paginator query must be type of array or an object, but got "%s".', $query
            ));
        }

        $queryClass = get_class($query);

        if (!isset(self::$registry[$queryClass])) {
            throw new \RuntimeException(sprintf('There is no registered paginator for "%s" query.', $queryClass));
        }

        $paginatorClass = self::$registry[$queryClass];

        return new $paginatorClass($query, $options);
    }


    public static function validateQueryClass(string $queryClass)
    {
        try {
            new \ReflectionClass($queryClass);
        } catch (\ReflectionException $e) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid namespace "%s" of the paginator query class.', $queryClass
            ));
        }
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
