<?php

namespace ArturDoruch\ListBundle\Templating;

use ArturDoruch\ListBundle\Request\QueryParameterNames;
use ArturDoruch\ListBundle\Request\QuerySortHelper;
use ArturDoruch\ListBundle\Sorting\SortChoiceCollection;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
class SortingHelper
{
    /**
     * @var RouteHelper
     */
    private $routeHelper;

    /**
     * @param RouteHelper $routeHelper
     */
    public function __construct(RouteHelper $routeHelper)
    {
        $this->routeHelper = $routeHelper;
    }

    /**
     * @param string $field
     * @param string $defaultDirection
     *
     * @return array
     */
    public function prepareSortLinkData(string $field, string $defaultDirection)
    {
        $queryParameters = $this->routeHelper->getQueryParameters();
        $currentValue = $queryParameters[QueryParameterNames::getSort()] ?? null;
        $direction = $defaultDirection;
        $isClicked = false;

        if ($currentValue) {
            $sort = QuerySortHelper::parse($currentValue);

            foreach ($sort as $currentField => $currentDirection) {
                if ($currentField === $field) {
                    $isClicked = true;
                    $direction = $currentDirection === 'asc' ? 'desc' : 'asc';
                }
            }
        }

        $url = $this->routeHelper->generateUrl(null, QuerySortHelper::create($field, $direction));

        return [
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
            $sort = QuerySortHelper::create($choice->getField(), $choice->getDirection());
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
