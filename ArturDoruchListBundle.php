<?php

namespace ArturDoruch\ListBundle;

use ArturDoruch\ListBundle\Paginator\PaginatorRegistry;
use ArturDoruch\ListBundle\Request\QueryParameterNames;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ArturDoruchListBundle extends Bundle
{
    /**
     * @inheritDoc
     */
    public function boot()
    {
        $names = $this->container->getParameter('arturdoruch_list.query_parameter_names');
        QueryParameterNames::setNames($names['page'], $names['limit'], $names['sort']);

        $paginatorProviders = $this->container->getParameter('arturdoruch_list.paginator_providers');
        foreach ($paginatorProviders as $queryClass => $paginatorClass) {
            PaginatorRegistry::add($queryClass, $paginatorClass);
        }
    }
}
