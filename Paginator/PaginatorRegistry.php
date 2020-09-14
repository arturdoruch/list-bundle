<?php

namespace ArturDoruch\ListBundle\Paginator;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
class PaginatorRegistry
{
    private static $instance;

    /**
     * @var PaginatorInterface[]
     */
    private $classes = [];

    public static function getInstance(): PaginatorRegistry
    {
        if (!self::$instance) {
            self::$instance = (new self())
                ->add(ArrayPaginator::class)
                ->add(DoctrinePaginator::class)
                ->add(DoctrineMongoDBPaginator::class)
                ->add(MongoDBPaginator::class);
        }

        return self::$instance;
    }

    private function __construct()
    {
    }

    /**
     * Registers paginator.
     *
     * @param string $paginatorClass The paginator class namespace. The paginator must implement
     *                               the ArturDoruch\ListBundle\Paginator\PaginatorInterface.
     *
     * @return $this
     */
    public function add(string $paginatorClass)
    {
        self::validatePaginatorClass($paginatorClass);
        $this->classes[] = $paginatorClass;

        return $this;
    }

    /**
     * Gets a new instance of the paginator for given query.
     *
     * @param mixed $query
     * @param array $options The paginator options.
     *
     * @return PaginatorInterface
     */
    public function get($query, array $options = [])
    {
        foreach ($this->classes as $paginatorClass) {
            if ($paginatorClass::supportsQuery($query)) {
                return new $paginatorClass($query, $options);
            }
        }

        throw new \RuntimeException(sprintf(
            'The paginator for query "%s" is not registered.', is_object($query) ? get_class($query) : gettype($query)
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
