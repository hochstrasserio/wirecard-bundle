<?php

namespace Hochstrasser\WirecardBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('hochstrasser_wirecard');

        $rootNode
            ->children()
                ->scalarNode('customer_id')->isRequired()->end()
                ->scalarNode('secret')->isRequired()->end()
                ->scalarNode('shop_id')->defaultValue(null)->end()
                ->scalarNode('language')->isRequired()->end()
                ->scalarNode('javascript_script_version')->defaultValue(null)->end()
                ->scalarNode('user_agent')->defaultValue('Hochstrasser/Wirecard')->end()
                ->scalarNode('backend_password')->defaultValue(null)->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
