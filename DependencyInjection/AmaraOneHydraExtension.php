<?php

namespace Amara\Bundle\OneHydraBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AmaraOneHydraExtension extends ConfigurableExtension
{
	/**
	 * Configures the passed container according to the merged configuration.
	 *
	 * @param array $mergedConfig
	 * @param ContainerBuilder $container
	 */
	protected function loadInternal(array $mergedConfig, ContainerBuilder $container) {

		$loader = new Loader\YamlFileLoader(
			$container,
			new FileLocator(__DIR__ . '/../Resources/config')
		);
		$loader->load('services.yml');


		if (array_key_exists('dateinterval', $mergedConfig)) {
			$container->setParameter('amara_one_hydra.dateinterval', $mergedConfig['dateinterval']);
		}

		if (array_key_exists('programs', $mergedConfig)) {
			$container->setParameter('amara_one_hydra.programs', $mergedConfig['programs']);
		}

		if (array_key_exists('dynamic_pageproxyservice', $mergedConfig)) {
			$container->setParameter('amara_one_hydra.dynamic_pageproxyservice', $mergedConfig['dynamic_pageproxyservice']);
		}

		if (array_key_exists('defaultProgramId', $mergedConfig)) {
			$container->setParameter('amara_one_hydra.defaultProgramId', $mergedConfig['defaultProgramId']);
		}

		if (array_key_exists('custom_program_solver_strategy', $mergedConfig)) {
			$container->setParameter('amara_one_hydra.custom_program_solver_strategy', $mergedConfig['custom_program_solver_strategy']);
		}

		if (array_key_exists('custom_pagename_transform_strategy', $mergedConfig)) {
			$container->setParameter('amara_one_hydra.custom_pagename_transform_strategy', $mergedConfig['custom_pagename_transform_strategy']);
		}
	
	}
}
