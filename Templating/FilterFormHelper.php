<?php

namespace ArturDoruch\ListBundle\Templating;

use ArturDoruch\ListBundle\Request\QueryParameterNames;
use Symfony\Component\Form\FormView;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
class FilterFormHelper
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
     * @param FormView $formView
     * @param bool $removeQuerySortParameter
     *
     * @return array
     */
    public function prepareFormData(FormView $formView, bool $removeQuerySortParameter)
    {
        $vars =& $formView->vars;

        if ('GET' !== $method = strtoupper($vars['method'])) {
            throw new \LogicException(sprintf('Invalid filter form method %s. Allowed method is GET.', $method));
        }

        $vars['attr']['novalidate'] = 'novalidate';

        $queryParameters = $this->routeHelper->getQueryParameters();
        unset($queryParameters[QueryParameterNames::getPage()]);
        unset($queryParameters[$formName = $vars['name']]);

        if ($removeQuerySortParameter) {
            unset($queryParameters[QueryParameterNames::getSort()]);
        }

        return [
            'form' => $formView,
            'action' => $this->routeHelper->generateFormActionUrl(),
            'formName' => $formName,
            'queryParameters' => $queryParameters,
        ];
    }
}
