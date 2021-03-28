<?php

namespace ArturDoruch\ListBundle\Templating;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
interface RouteHelperInterface
{
    /**
     * Generates list page URL for the current route.
     *
     * @param int $page Page number.
     * @param string $sort Sorting direction.
     *
     * @return string
     */
    public function generateUrl(?int $page = null, ?string $sort = null): string;

    /**
     * Generates URL for using in a list filter form.
     *
     * @param bool $resetPage Whether to set page number to 1.
     *
     * @return string
     */
    public function generateFormActionUrl(bool $resetPage = true): string;

    /**
     * Gets query parameters of the current request.
     *
     * @return array
     */
    public function getQueryParameters(): array;
}
