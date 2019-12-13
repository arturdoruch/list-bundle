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
     * https://specs.openstack.org/openstack/api-wg/guidelines/pagination_filter_sort.html
     *
     * @param Environment $environment
     * @param string $label
     * @param string $field
     * @param string $defaultDirection One of the value "asc", "desc".
     *
     * @return string
     */
    public function renderSortLink(Environment $environment, $label, $field, $defaultDirection = 'asc')
    {
        $data = $this->sortingHelper->prepareSortLinkData($label, $field, $defaultDirection);

        return $environment->render('@ArturDoruchList/sorting/sortLink.html.twig', $data);
    }

    /**
     * Renders HTML select element with options to sort list items.
     *
     * @param Environment $environment
     * @param SortChoiceCollection $sortChoiceCollection
     * @return string
     * @internal param array $choices Collection of sorting choices with keys:
     *  - label
     *  - field
     *  - direction
     *
     */
    public function renderSortForm(Environment $environment, SortChoiceCollection $sortChoiceCollection)
    {
        $data = $this->sortingHelper->prepareSortFormData($sortChoiceCollection);

        return $environment->render('@ArturDoruchList/sorting/sortForm.html.twig', $data);
    }
}
 