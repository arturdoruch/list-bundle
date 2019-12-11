<?php

namespace ArturDoruch\ListBundle\Sorting;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
class SortChoice
{
    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $direction;

    /**
     * @param string $label
     * @param string $field
     * @param string $direction
     */
    public function __construct($label, $field, $direction)
    {
        $this->label = $label;
        $this->field = $field;
        $this->direction = strtolower($direction) === 'desc' ? 'desc': 'asc';
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getDirection(): string
    {
        return $this->direction;
    }
}
