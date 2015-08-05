<?php
namespace Amara\Bundle\OneHydraBundle\DependencyInjection\Compiler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ProxyPass implements CompilerPassInterface {

	/**
	 * You can modify the container here before it is dumped to PHP code.
	 *
	 * @param ContainerBuilder $container
	 *
	 * @api
	 */
	public function process(ContainerBuilder $container) {

		if ($container->hasParameter('amara_one_hydra.dynamic_pageproxyservice')) {

			// Get the new definition
			$newDefinition = $container->findDefinition($container->getParameter('amara_one_hydra.dynamic_pageproxyservice'));

			// Replace with the new one
			$container->setDefinition('onehydra_pageproxy', $newDefinition);

		}
	}
}