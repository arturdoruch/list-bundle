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
     * @param string $direction Sorting direction. One of the values "asc", "desc".
     *
     * @return $this
     */
    public function add(string $label, string $field, string $direction)
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
