<?php

namespace KRG\SeoBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('krg_seo');

        $rootNode
            ->children()
            ->scalarNode('seo_class')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('seo_page_class')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('title_prefix')->end()
            ->scalarNode('title_suffix')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
