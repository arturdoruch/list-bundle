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

    /**
     * @var Environment
     */
    private $environment;

    /**
     * @var array Filtering config.
     */
    private $filterFormConfig = [
        'display_reset_button' => true,
        'reset_sorting' => false,
    ];

    public function __construct(FilterFormHelper $filterFormHelper, Environment $environment, array $filterFormConfig)
    {
        $this->filterFormHelper = $filterFormHelper;
        $this->environment = $environment;
        $this->filterFormConfig = $filterFormConfig + $this->filterFormConfig;
    }


    public function getFunctions()
    {
        return [
            new TwigFunction('arturdoruch_list_filter_form', [$this, 'renderFilterForm'], [
                'is_safe' => ['html']
            ])
        ];
    }

    /**
     * @param FormView $formView
     * @param array $config
     *  - reset_sorting (bool) Whether to reset list sorting after filtering the list.
     *                         If true query "sort" parameter is removed from the request query.
     *  - display_reset_button (bool) Whether to display button resetting the filter form elements.
     *
     * @return string
     */
    public function renderFilterForm(FormView $formView, array $config = [])
    {
        $config += $this->filterFormConfig;

        $data = $this->filterFormHelper->prepareFormData($formView, $config['reset_sorting']);
        $data['displayResetButton'] = $config['display_reset_button'];

        $html = $this->environment->render('@ArturDoruchList/filtering/filterForm.html.twig', $data);
        // Add input elements with data required by JavaScript scripts.
        $html .= '<input type="hidden" name="list__query-parameter-names" value="' . htmlspecialchars(json_encode(QueryParameterNames::all())) . '">';
        $html .= '<input type="hidden" name="list__filter-form__reset-sorting[' . $formView->vars['name'] . ']" value="' . (int) ($config['reset_sorting'] === true) . '">';

        return $html;
    }
}
 