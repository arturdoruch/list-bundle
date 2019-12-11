<?php

namespace ArturDoruch\ListBundle;

use ArturDoruch\ListBundle\Paginator\PaginatorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
class Pagination
{
    /**
     * @var int
     */
    private $totalItems;

    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var \ArrayIterator
     */
    private $items;

    /**
     * @var array
     */
    private $itemLimits = [];

    /**
     * @param PaginatorInterface $paginator
     * @param int $page
     * @param int $limit
     */
    public function __construct(PaginatorInterface $paginator, $page, $limit)
    {
        $this->page = (int) $page;
        $this->limit = (int) $limit;

        if ($this->limit < 0) {
            $this->limit = 0;
        }

        if ($this->page < 1 || $this->limit === 0) {
            $this->page = 1;
        }

        $this->offset = ($this->page - 1) * $this->limit;
        $this->totalItems = $paginator->getTotalItems();

        if ($this->offset >= $this->totalItems && $this->totalItems > 0) {
            throw new NotFoundHttpException(sprintf('The page "%d" does not exist.', $this->page));
        }

        $this->items = $paginator->getItems($this->offset, $this->limit);
    }

    /**
     * @return \ArrayIterator
     */
    public function getItems(): \ArrayIterator
    {
        return $this->items;
    }

    /**
     * @param \ArrayIterator $items
     */
    public function setItems(\ArrayIterator $items)
    {
        $this->items = $items;
    }

    /**
     * @return int
     */
    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    /**
     * @return int
     */
    public function getTotalPages(): int
    {
        if ($this->limit < 1) {
            return 1;
        }

        return (int) ceil($this->totalItems / $this->limit);
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return int The positive integer or 0 if no limit.
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getPreviousPage(): int
    {
        return $this->page - 1;
    }

    /**
     * @return int
     */
    public function getNextPage(): int
    {
        return $this->page + 1;
    }

    /**
     * @return bool
     */
    public function hasPreviousPage(): bool
    {
        return $this->getPreviousPage() >= 1;
    }

    /**
     * @return bool
     */
    public function hasNextPage(): bool
    {
        return $this->getNextPage() <= $this->getTotalPages();
    }

    /**
     * Gets index of the first item on the list items.
     *
     * @return int|null
     */
    public function getFirstItemIndex(): ?int
    {
        if ($this->getTotalItems() === 0) {
            return 0;
        }

        return $this->offset + 1;
    }

    /**
     * Gets index of the lest item on the list items.
     *
     * @return int|null
     */
    public function getLastItemIndex(): ?int
    {
        if (0 === ($totalItems = $this->getTotalItems())) {
            return 0;
        }

        $lastIndex = $this->page * $this->limit;

        if (!$lastIndex || $lastIndex >= $totalItems) {
            $lastIndex = $totalItems;
        }

        return $lastIndex;
    }

    /**
     * @return array
     */
    public function getItemLimits(): array
    {
        return $this->itemLimits;
    }

    /**
     * @param integer[] $limits
     */
    public function setItemLimits(array $limits)
    {
        $this->itemLimits = array_map(function ($limit) {
            return (int) $limit;
        }, $limits);
    }
}
 