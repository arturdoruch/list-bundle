<?php

namespace ArturDoruch\ListBundle;

use ArturDoruch\ListBundle\Paginator\PaginatorRegistry;
use ArturDoruch\ListBundle\Request\QueryParameterNames;
use ArturDoruch\ListBundle\Request\QuerySort;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ArturDoruchListBundle extends Bundle
{
    public function boot()
    {
        $parameterNames = $this->container->getParameter('arturdoruch_list.query_parameter_names');
        QueryParameterNames::setNames($parameterNames['page'], $parameterNames['limit'], $parameterNames['sort']);

        if ($sortDirectionConfig = $this->container->getParameter('arturdoruch_list.query_sort_direction')) {
            QuerySort::setDirectionConfig(
                $sortDirectionConfig['asc'],
                $sortDirectionConfig['desc'],
                $sortDirectionConfig['position'],
                $sortDirectionConfig['separator']
            );
        }

        if ($paginators = $this->container->getParameter('arturdoruch_list.paginators')) {
            $paginatorRegistry = PaginatorRegistry::getInstance();

            foreach ($paginators as $paginatorClass) {
                $paginatorRegistry->add($paginatorClass);
            }
        }
    }
}
