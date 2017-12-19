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
use Symfony\Component\DependencyInjection\Alias;
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

        $container->setAlias('amara_one_hydra.api', new Alias($mergedConfig['api_service'], true));
        $container->setAlias('amara_one_hydra.api.http_request_builder', new Alias($mergedConfig['http_request_builder_service'], true));
        $container->setAlias('amara_one_hydra.api.transport', new Alias($mergedConfig['transport_service'], true));
        $container->setAlias('amara_one_hydra.api.result_builder_engine', new Alias($mergedConfig['result_builder_engine_service'], true));
        $container->setAlias('amara_one_hydra.page_transform_strategy', new Alias($mergedConfig['page_transform_strategy_service'], true));
        $container->setAlias('amara_one_hydra.page_manager',  new Alias($mergedConfig['page_manager_service'], true));
        $container->setAlias('amara_one_hydra.page_storage', new Alias($mergedConfig['page_storage_service'], true));

        $container->setParameter('amara_one_hydra.is_uat', $mergedConfig['is_uat']);
        $container->setParameter('amara_one_hydra.is_not_uat', !$mergedConfig['is_uat']);
        $container->setParameter('amara_one_hydra.dateinterval', $mergedConfig['dateinterval']);
        $container->setParameter('amara_one_hydra.programs', $mergedConfig['programs']);
    }
}
