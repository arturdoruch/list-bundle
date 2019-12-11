<?php

namespace ArturDoruch\ListBundle\Templating;

use ArturDoruch\ListBundle\Pagination;
use ArturDoruch\ListBundle\Request\QueryParameterNames;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
class PaginationHelper
{
    /**
     * @var RouteHelper
     */
    private $routeHelper;

    private $pageItemsConfig = [
        'page_items_limit' => 3,
        'prev_page_label' => '&#8592; Prev',
        'next_page_label' => 'Next &#8594;',
    ];

    /**
     * @var array
     */
    private $itemLimits;

    /**
     * @param RouteHelper $routeHelper
     * @param array $pageItemsConfig
     * @param array $itemLimits The default pagination item limits.
     */
    public function __construct(RouteHelper $routeHelper, array $pageItemsConfig, array $itemLimits)
    {
        $this->routeHelper = $routeHelper;
        $this->pageItemsConfig = $pageItemsConfig + $this->pageItemsConfig;
        $this->itemLimits = $itemLimits;
    }

    /**
     * Prepares pagination page items data.
     *
     * @param Pagination $pagination
     *
     * @return array
     */
    public function preparePaginationItemsData(Pagination $pagination)
    {
        $items = [];

        $pageItemsLimit = $this->pageItemsConfig['page_items_limit'];
        $totalPages = $pagination->getTotalPages();

        if ($totalPages <= 1 || $pagination->getPage() > $pagination->getTotalPages()) {
            return $items;
        }

        // Prev page
        if ($pagination->hasPreviousPage()) {
            $items[] = $this->createPageItemData($this->pageItemsConfig['prev_page_label'], $pagination->getPreviousPage());
        }

        for ($i = 1; $i <= $totalPages; $i++) {
            // Current page
            if ($i === $pagination->getPage()) {
                $items[] = $this->createPageItemData($i);

                continue;
            }

            $minPaginablePage = $pagination->getPage() - $pageItemsLimit;
            $maxPaginablePage = $pagination->getPage() + $pageItemsLimit;
            // Skip pages
            if (($i === 2 && $minPaginablePage > 2) || $i == $totalPages && $maxPaginablePage+1 < $totalPages) {
                $items[] = [
                    'skipped' => true
                ];
            }
            // First, last and other pages
            if (($i - $pageItemsLimit <= $pagination->getPage() && $i + $pageItemsLimit >= $pagination->getPage())
                || $i === 1 || $i === $totalPages
            ) {
                $items[] = $this->createPageItemData($i, $i);

            // Middle pages
            } elseif ($i > 2 && $i < $minPaginablePage) {
                $middlePage = $minPaginablePage;
                if ($middlePage % 2 !== 0) {
                    $middlePage--;
                }
                if ($middlePage / $i === 2) {
                    $items[] = $this->createPageItemData($i, $i);
                }
            } elseif ($i > $maxPaginablePage && $totalPages - $maxPaginablePage >= 4) {
                $middlePage = ceil(($totalPages - $maxPaginablePage) / 2) + $maxPaginablePage;
                if ($i == $middlePage) {
                    $items[] = $this->createPageItemData($i, $i);
                }
            }
        }
        // Next page
        if ($pagination->hasNextPage()) {
            $items[] = $this->createPageItemData($this->pageItemsConfig['next_page_label'], $pagination->getNextPage());
        }

        return $items;
    }

    /**
     * @param string $label
     * @param int $page
     *
     * @return array
     */
    private function createPageItemData($label, $page = null)
    {
        $data['label'] = $label;

        if ($page) {
            $data['url'] = $this->routeHelper->generateUrl($page);
        } else {
            $data['active'] = true;
        }

        return $data;
    }

    /**
     * @param Pagination $pagination
     *
     * @return array
     */
    public function prepareLimitFormData(Pagination $pagination)
    {
        $queryParameters = $this->routeHelper->getQueryParameters();
        //$queryParameters[QueryParameterNames::getPage()] = 1;
        unset($queryParameters[QueryParameterNames::getPage()]);
        unset($queryParameters[QueryParameterNames::getLimit()]);

        return [
            'action' => $this->routeHelper->generateFormActionUrl(),
            'limit' => [
                'name' => QueryParameterNames::getLimit(),
                'values' => $pagination->getItemLimits() ?: $this->itemLimits,
                'currentValue' => $pagination->getLimit()
            ],
            'queryParameters' => $queryParameters
        ];
    }
}
