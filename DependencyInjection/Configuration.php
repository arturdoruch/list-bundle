<?php

namespace ArturDoruch\ListBundle\DependencyInjection;

use ArturDoruch\ListBundle\Paginator\PaginatorRegistry;
use ArturDoruch\ListBundle\Request\QuerySort;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class Configuration implements ConfigurationInterface
{
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
                        ->scalarNode('asc')->defaultValue('asc')->end()
                        ->scalarNode('desc')->defaultValue('desc')->end()
                        ->enumNode('position')
                            ->values(QuerySort::getDirectionPositions())
                            ->defaultValue('after')
                        ->end()
                        ->scalarNode('separator')->defaultValue(':')->end()
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
                ->arrayNode('paginators')
                    ->beforeNormalization()
                        ->ifArray()
                        ->then(function ($v) {
                            foreach ($v as $paginatorClass) {
                                try {
                                    PaginatorRegistry::validatePaginatorClass($paginatorClass);
                                } catch (\InvalidArgumentException $e) {
                                    throw new InvalidConfigurationException(
                                        'Invalid value for path "artur_doruch_list.paginators": '. $e->getMessage()
                                    );
                                }
                            }
                        })
                    ->end()
                    ->info('Paginator class namespaces.')
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('filter_form')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('display_reset_button')->defaultTrue()
                            ->info('Whether to display button resetting the filter form elements.')
                        ->end()
                        ->booleanNode('reset_sorting')->defaultFalse()
                            ->info('Whether to reset list sorting after filtering the list.')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
