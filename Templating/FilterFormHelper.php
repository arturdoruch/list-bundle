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
     * @var RouteHelperInterface
     */
    private $routeHelper;

    public function __construct(RouteHelperInterface $routeHelper)
    {
        $this->routeHelper = $routeHelper;
    }

    /**
     * @param FormView $formView
     * @param bool $removeQuerySortParameter
     *
     * @return array
     */
    public function prepareFormData(FormView $formView, bool $removeQuerySortParameter): array
    {
        $vars =& $formView->vars;

        if ('GET' !== $method = strtoupper($vars['method'])) {
            throw new \LogicException(sprintf('Invalid filter form method %s. Allowed method is GET.', $method));
        }

        $vars['attr']['novalidate'] = 'novalidate';

        $queryParameters = $this->routeHelper->getQueryParameters();
        unset($queryParameters[$formName = $vars['name']]);
        unset($queryParameters[QueryParameterNames::getPage()]);

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
