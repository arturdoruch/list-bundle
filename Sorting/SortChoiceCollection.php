<?php

namespace ArturDoruch\ListBundle\Sorting;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
class SortChoiceCollection
{
    /**
     * @var SortChoice[]
     */
    private $choices = [];

    /**
     * @param string $label
     * @param string $field
     * @param string $direction
     *
     * @return $this
     */
    public function add($label, $field, $direction)
    {
        $this->choices[] = new SortChoice($label, $field, $direction);

        return $this;
    }

    /**
     * @return SortChoice[]
     */
    public function all(): array
    {
        return $this->choices;
    }
}
