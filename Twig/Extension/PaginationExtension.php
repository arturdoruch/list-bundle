<?php

namespace ArturDoruch\ListBundle\Twig\Extension;

use ArturDoruch\ListBundle\Pagination;
use ArturDoruch\ListBundle\Templating\PaginationHelper;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
class PaginationExtension extends AbstractExtension
{
    /**
     * @var PaginationHelper
     */
    private $paginationHelper;


    public function __construct(PaginationHelper $paginationHelper)
    {
        $this->paginationHelper = $paginationHelper;
    }


    public function getFunctions()
    {
        $options = [
            'is_safe' => ['html'],
            'needs_environment' => true
        ];

        return [
            new TwigFunction('arturdoruch_list_pagination', [$this, 'renderPagination'], $options),
            new TwigFunction('arturdoruch_list_displayed_items', [$this, 'renderDisplayedItems'], $options),
            new TwigFunction('arturdoruch_list_items_limit_form', [$this, 'renderItemsLimitForm'], $options),
            new TwigFunction('arturdoruch_list_items_and_pagination', [$this, 'renderItemsAndPagination'], $options),
        ];
    }

    /**
     * Renders pagination list items.
     *
     * @param Environment $environment
     * @param Pagination $pagination
     *
     * @return string
     */
    public function renderPagination(Environment $environment, Pagination $pagination)
    {
        return $environment->render('@ArturDoruchList/pagination/pagination.html.twig', [
            'items' => $this->paginationHelper->preparePaginationItemsData($pagination)
        ]);
    }

    /**
     * Renders range of displayed items.
     *
     * @param Environment $environment
     * @param Pagination $pagination
     *
     * @return string
     */
    public function renderDisplayedItems(Environment $environment, Pagination $pagination)
    {
        return $environment->render('@ArturDoruchList/pagination/displayedItems.html.twig', [
            'firstItemIndex' => $pagination->getFirstItemIndex(),
            'lastItemIndex' => $pagination->getLastItemIndex(),
            'totalItems' => $pagination->getTotalItems()
        ]);
    }

    /**
     * Renders form and select field to change (items limit) displayed number items per page.
     *
     * @param Environment $environment
     * @param Pagination $pagination
     *
     * @return string
     */
    public function renderItemsLimitForm(Environment $environment, Pagination $pagination)
    {
        $data = $this->paginationHelper->prepareLimitFormData($pagination);

        return $environment->render('@ArturDoruchList/pagination/itemsLimitForm.html.twig', $data);
    }

    /**
     * Renders elements: displayed items info, items limit form and pagination.
     *
     * @param Environment $environment
     * @param Pagination $pagination
     *
     * @return string
     */
    public function renderItemsAndPagination(Environment $environment, Pagination $pagination)
    {
        return $environment->render('@ArturDoruchList/pagination/itemsAndPagination.html.twig', [
            'pagination' => $pagination
        ]);
    }
}
 