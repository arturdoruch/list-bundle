<?php

namespace ArturDoruch\ListBundle;

use ArturDoruch\ListBundle\Sorting\SortChoiceCollection;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
class ItemList implements \IteratorAggregate, \Countable
{
    /**
     * @var Pagination
     */
    private $pagination;

    /**
     * @var FormView
     */
    private $filterForm;

    /**
     * @var SortChoiceCollection
     */
    private $sortChoiceCollection;

    /**
     * @param Pagination $pagination
     * @param FormInterface $filterForm
     * @param SortChoiceCollection $sortChoiceCollection
     */
    public function __construct(Pagination $pagination, ?FormInterface $filterForm = null, SortChoiceCollection $sortChoiceCollection = null)
    {
        $this->pagination = $pagination;
        $this->filterForm = $filterForm->createView();
        $this->sortChoiceCollection = $sortChoiceCollection;
    }

    /**
     * @return Pagination
     */
    public function getPagination(): Pagination
    {
        return $this->pagination;
    }

    /**
     * @return FormView
     */
    public function getFilterForm(): ?FormView
    {
        return $this->filterForm;
    }

    /**
     * @return SortChoiceCollection
     */
    public function getSortChoiceCollection(): ?SortChoiceCollection
    {
        return $this->sortChoiceCollection;
    }

    /**
     * Gets list paginated items.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return $this->pagination->getItems();
    }

    /**
     * Counts total list items.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->getIterator());
    }

    /**
     * Checks if the list has no items.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }
}
