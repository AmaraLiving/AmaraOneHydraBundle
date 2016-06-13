<?php

/*
 * This file is part of the AmaraOneHydraBundle package.
 *
 * (c) Amara Living Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Amara\Bundle\OneHydraBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('amara_one_hydra');
        $rootNode
            ->children()
                ->scalarNode('api_service')->defaultValue('amara_one_hydra.api.default')->end()
                ->scalarNode('http_request_builder_service')->defaultValue('amara_one_hydra.api.http_request_builder.default')->end()
                ->scalarNode('transport_service')->defaultValue('amara_one_hydra.api.transport.guzzle')->end()
                ->scalarNode('result_builder_engine_service')->defaultValue('amara_one_hydra.api.result_builder_engine.default')->end()
                ->scalarNode('page_transform_strategy_service')->defaultValue('amara_one_hydra.page_transform_strategy.default')->end()
                ->scalarNode('page_manager_service')->defaultValue('amara_one_hydra.page_manager.default')->end()
                ->scalarNode('page_storage_service')->defaultValue('amara_one_hydra.page_storage.default')->end()

                ->scalarNode('is_uat')->defaultValue(true)->end()
                ->scalarNode('dateinterval')->defaultValue('PT15M')->end()

                ->arrayNode('programs')->isRequired()
                    ->useAttributeAsKey('program_id')
                    ->normalizeKeys(false)
                    ->prototype('array')
                        ->normalizeKeys(false)
                        ->children()
                            ->scalarNode('auth_token')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
