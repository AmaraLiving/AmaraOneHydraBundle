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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * AmaraOneHydraExtension
 */
class AmaraOneHydraExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yml');

        $container->setAlias('amara_one_hydra.api', $mergedConfig['api_service']);
        $container->setAlias('amara_one_hydra.api.http_request_builder', $mergedConfig['http_request_builder_service']);
        $container->setAlias('amara_one_hydra.api.transport', $mergedConfig['transport_service']);
        $container->setAlias('amara_one_hydra.api.result_builder_engine', $mergedConfig['result_builder_engine_service']);
        $container->setAlias('amara_one_hydra.page_transform_strategy', $mergedConfig['page_transform_strategy_service']);
        $container->setAlias('amara_one_hydra.page_manager', $mergedConfig['page_manager_service']);
        $container->setAlias('amara_one_hydra.page_storage', $mergedConfig['page_storage_service']);

        $container->setParameter('amara_one_hydra.is_uat', $mergedConfig['is_uat']);
        $container->setParameter('amara_one_hydra.is_not_uat', !$mergedConfig['is_uat']);
        $container->setParameter('amara_one_hydra.dateinterval', $mergedConfig['dateinterval']);
        $container->setParameter('amara_one_hydra.programs', $mergedConfig['programs']);
    }
}
