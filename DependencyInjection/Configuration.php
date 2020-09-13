<?php

namespace ArturDoruch\ListBundle\DependencyInjection;

use ArturDoruch\ListBundle\Paginator\PaginatorRegistry;
use ArturDoruch\ListBundle\Request\QuerySort;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class Configuration implements ConfigurationInterface
{
    /**
     * @todo Add providers configuration.
     *
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('artur_doruch_list');

        $rootNode
            ->children()
                ->arrayNode('query_parameter_names')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('page')->defaultValue('page')->end()
                        ->scalarNode('limit')->defaultValue('limit')->end()
                        ->scalarNode('sort')->defaultValue('sort')->end()
                    ->end()
                ->end()
                ->arrayNode('query_sort_direction')
                    ->children()
                        ->scalarNode('asc')->treatNullLike('')->end()
                        ->scalarNode('desc')->treatNullLike('')->end()
                        ->enumNode('position')->values(QuerySort::getDirectionPositions())->end()
                        ->scalarNode('separator')->treatNullLike('')->end()
                    ->end()
                ->end()
                ->arrayNode('pagination')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('item_limits')
                            ->scalarPrototype()
                            ->end()
                        ->end()
                        ->arrayNode('page_items')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('prev_page_label')->defaultValue('&#8592; Prev')->end()
                                ->scalarNode('next_page_label')->defaultValue('Next &#8594;')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('paginator_providers')
                    ->beforeNormalization()
                        ->ifArray()
                        ->then(function ($v) {
                            foreach ($v as $queryClass => $providerClass) {
                                try {
                                    PaginatorRegistry::validateQueryClass($queryClass);
                                    PaginatorRegistry::validatePaginatorClass($providerClass);
                                } catch (\InvalidArgumentException $e) {
                                    throw new InvalidConfigurationException(
                                        'Invalid configuration for path "artur_doruch_list.paginator_providers": '. $e->getMessage()
                                    );
                                }
                            }
                        })
                    ->end()
                    ->info('The collection of paginator providers with "query class: paginator class" pairs.')
                    ->scalarPrototype()->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
