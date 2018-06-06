<?php

namespace KRG\CmsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('krg_cms');

        $rootNode
            ->children()
                ->arrayNode('seo')
                    ->children()
                        ->arrayNode('title')
                            ->children()
                                ->scalarNode('suffix')->defaultNull()->end()
                            ->end()
                        ->end()
                        ->booleanNode('og')->defaultFalse()->end()
                    ->end()
                ->end()
                ->arrayNode('page')
                    ->children()
                        ->arrayNode('extra_hide_elements')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('blocks_path')
                    ->prototype('scalar')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
