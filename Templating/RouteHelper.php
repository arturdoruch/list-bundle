<?php

namespace ArturDoruch\ListBundle\Templating;

use ArturDoruch\ListBundle\Request\QueryParameterNames;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * The helper methods for the current route.
 *
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
class RouteHelper
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var array The current route data.
     */
    private $data = ['path' => null];

    /**
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Generates page url for current route.
     *
     * @param int $page
     * @param string $sort
     *
     * @return string
     */
    public function generateUrl($page = null, string $sort = null)
    {
        $data = $this->getData();
        $parameters = $data['routeAndQueryParameters'];

        if ($page) {
            $parameters[QueryParameterNames::getPage()] = $page;
        }

        if ($sort) {
            $parameters[QueryParameterNames::getSort()] = $sort;
        }

        return $this->router->generate($data['name'], $parameters);
    }

    /**
     * @return string
     */
    public function generateFormActionUrl(bool $resetPage = true)
    {
        $data = $this->getData();
        $parameters = $data['parameters'];

        if ($resetPage && isset($parameters[QueryParameterNames::getPage()])) {
            $parameters[QueryParameterNames::getPage()] = 1;
        }

        return $this->router->generate($data['name'], $parameters);
    }

    /*
     * @return string|null
     */
    /*public function getSortQueryParameter(): ?string
    {
        return $this->getQueryParameters()[QueryParameterNames::getSort()] ?? null;
    }*/

    /**
     * Gets request query parameters.
     *
     * @return array
     */
    public function getQueryParameters(): array
    {
        return $this->getData()['queryParameters'];
    }

    /**
     * @return array
     *  - name (string) The route name.
     *  - parameters (array) Route and query parameters.
     *  - queryParameters (array)
     */
    private function getData()
    {
        $context = $this->router->getContext();
        $pathInfo = $context->getPathInfo();

        if ($pathInfo !== $this->data['path']) {
            $parameters = $this->router->match($context->getPathInfo());
            $name = $parameters['_route'];

            foreach ($parameters as $index => $parameter) {
                if (strpos($index, '_') === 0) {
                    unset($parameters[$index]);
                }
            }

            parse_str($context->getQueryString(), $queryParameters);
            $routeAndQueryParameters = array_merge($parameters, $queryParameters);

            $this->data = [
                'path' => $pathInfo,
                'name' => $name,
                'parameters' => $parameters,
                'queryParameters' => $queryParameters,
                'routeAndQueryParameters' => $routeAndQueryParameters,
            ];
        }

        return $this->data;
    }
}
