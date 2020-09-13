<?php

namespace ArturDoruch\ListBundle\Twig\Extension;

use ArturDoruch\ListBundle\Sorting\SortChoiceCollection;
use ArturDoruch\ListBundle\Templating\SortingHelper;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
class SortingExtension extends AbstractExtension
{
    /**
     * @var SortingHelper
     */
    private $sortingHelper;

    public function __construct(SortingHelper $sortingHelper)
    {
        $this->sortingHelper = $sortingHelper;
    }


    public function getFunctions()
    {
        $options = [
            'is_safe' => ['html'],
            'needs_environment' => true
        ];

        return [
            new TwigFunction('arturdoruch_list_sort_link', [$this, 'renderSortLink'], $options),
            new TwigFunction('arturdoruch_list_sort_form', [$this, 'renderSortForm'], $options),
        ];
    }

    /**
     * Renders a link (an anchor) sorting the list items.
     *
     * https://specs.openstack.org/openstack/api-wg/guidelines/pagination_filter_sort.html
     *
     * @param Environment $environment
     * @param string $label
     * @param string $field
     * @param string $initialDirection Initial sort direction. One of the values: "asc", "desc".
     *
     * @return string
     */
    public function renderSortLink(Environment $environment, $label, $field, $initialDirection = 'asc')
    {
        $data = $this->sortingHelper->prepareSortLinkData($label, $field, $initialDirection);

        return $environment->render('@ArturDoruchList/sorting/sortLink.html.twig', $data);
    }

    /**
     * Renders select element with options to sort list items.
     *
     * @param Environment $environment
     * @param SortChoiceCollection $sortChoiceCollection
     *
     * @return string
     */
    public function renderSortForm(Environment $environment, SortChoiceCollection $sortChoiceCollection)
    {
        $data = $this->sortingHelper->prepareSortFormData($sortChoiceCollection);

        return $environment->render('@ArturDoruchList/sorting/sortForm.html.twig', $data);
    }
}
 