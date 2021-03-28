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
     *  - name (string) The form name.
     *  - action (string) The form action URL.
     *  - queryParameters (array) The URL query parameters of the current route, except: "form-name", "page" and "sort".
     */
    public function prepareFormData(FormView $formView, bool $removeQuerySortParameter): array
    {
        $vars =& $formView->vars;

        if ('GET' !== $method = strtoupper($vars['method'])) {
            throw new \LogicException(sprintf('Invalid filter form method %s. Allowed method is GET.', $method));
        }

        $vars['attr']['novalidate'] = 'novalidate';

        $queryParameters = $this->routeHelper->getQueryParameters();
        unset($queryParameters[$name = $vars['name']]);
        unset($queryParameters[QueryParameterNames::getPage()]);

        if ($removeQuerySortParameter) {
            unset($queryParameters[QueryParameterNames::getSort()]);
        }

        return [
            'name' => $name,
            'action' => $this->routeHelper->generateFormActionUrl(),
            'queryParameters' => $queryParameters,
        ];
    }
}
