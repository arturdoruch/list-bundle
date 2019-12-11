<?php

namespace ArturDoruch\ListBundle\Twig\Extension;

use ArturDoruch\ListBundle\Request\QueryParameterNames;
use ArturDoruch\ListBundle\Templating\FilterFormHelper;
use Symfony\Component\Form\FormView;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
class FilteringExtension extends AbstractExtension
{
    /**
     * @var FilterFormHelper
     */
    private $filterFormHelper;

    public function __construct(FilterFormHelper $filterFormHelper)
    {
        $this->filterFormHelper = $filterFormHelper;
    }


    public function getFunctions()
    {
        return [
            new TwigFunction('arturdoruch_list_filter_form', [$this, 'renderFilterForm'], [
                'needs_environment' => true,
                'is_safe' => ['html']
            ])
        ];
    }

    /**
     * @param Environment $environment
     * @param FormView $formView
     * @param bool $removeQuerySortParameter Whether to remove query sort parameter from send form data.
     *
     * @return string
     */
    public function renderFilterForm(Environment $environment, FormView $formView, bool $removeQuerySortParameter = false)
    {
        $data = $this->filterFormHelper->prepareFormData($formView, $removeQuerySortParameter);

        return $environment->render('@ArturDoruchList/filtering/filterForm.html.twig', $data) .
        "<div data-query-parameter-names='" . json_encode(QueryParameterNames::all()) . "'></div>";
    }
}
 