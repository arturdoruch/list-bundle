<?php

namespace ArturDoruch\ListBundle\Templating;

use ArturDoruch\ListBundle\Request\QueryParameterNames;
use ArturDoruch\ListBundle\Request\QuerySort;
use ArturDoruch\ListBundle\Sorting\SortChoiceCollection;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
class SortingHelper
{
    /**
     * @var RouteHelperInterface
     */
    private $routeHelper;

    public function __construct(RouteHelperInterface $routeHelper)
    {
        $this->routeHelper = $routeHelper;
    }

    /**
     * @param string $label
     * @param string $field
     * @param string $initialDirection
     *
     * @return array
     */
    public function prepareSortLinkData(string $label, string $field, string $initialDirection)
    {
        $queryParameters = $this->routeHelper->getQueryParameters();
        $currentValue = $queryParameters[QueryParameterNames::getSort()] ?? null;
        $direction = $initialDirection;
        $isClicked = false;

        if ($currentValue) {
            $sortData = QuerySort::parse($currentValue);

            if (array_keys($sortData)[0] === $field) {
                $isClicked = true;
                $direction = QuerySort::getOppositeDirection($sortData[$field]);
            }
        }

        $url = $this->routeHelper->generateUrl(null, QuerySort::create($field, $direction));

        return [
            'label' => $label,
            'url' => $url,
            'isClicked' => $isClicked,
            'direction' => $direction,
        ];
    }

    /**
     * @param SortChoiceCollection $sortChoiceCollection
     *
     * @return array
     */
    public function prepareSortFormData(SortChoiceCollection $sortChoiceCollection)
    {
        $queryParameters = $this->routeHelper->getQueryParameters();
        $currentValue = $queryParameters[QueryParameterNames::getSort()] ?? null;
        unset($queryParameters[QueryParameterNames::getSort()]);
        $options = [];
        $selected = null;

        foreach ($sortChoiceCollection->all() as $choice) {
            $sort = QuerySort::create($choice->getField(), $choice->getDirection());
            $options[$sort] = $choice->getLabel();
        }

        return [
            'action' => $this->routeHelper->generateFormActionUrl(false),
            'sort' => [
                'name' => QueryParameterNames::getSort(),
                'options' => $options,
                'currentValue' => $currentValue,
            ],
            'queryParameters' => $queryParameters
        ];
    }
}
