<?php

namespace ArturDoruch\ListBundle\Templating;

use ArturDoruch\ListBundle\Request\QueryParameterNames;
use Symfony\Component\Routing\RouterInterface;

/**
 * The helper methods for the current route.
 *
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
class RouteHelper implements RouteHelperInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var array The current route data.
     */
    private $data = ['path' => null];

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function generateUrl(?int $page = null, ?string $sort = null): string
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
     * {@inheritdoc}
     */
    public function generateFormActionUrl(bool $resetPage = true): string
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
     * {@inheritdoc}
     */
    public function getQueryParameters(): array
    {
        return $this->getData()['queryParameters'];
    }

    /**
     * @return array The route data.
     */
    private function getData(): array
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
