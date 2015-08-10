<?php

namespace Amara\Bundle\OneHydraBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface {
	/**
	 * {@inheritDoc}
	 */
	public function getConfigTreeBuilder() {
		$treeBuilder = new TreeBuilder();
		$rootNode = $treeBuilder->root('amara_one_hydra');
		$rootNode->children()
			->variableNode('defaultProgramId')->defaultValue('www')->end()
			->variableNode('pageproxy')->end()
			->variableNode('dynamic_pageproxyservice')->defaultValue('onehydra_pageproxy')->end()
			->variableNode('program_solver_strategy')->defaultValue('onehydra_program_solver_strategy')->end()
			->scalarNode('dateinterval')->defaultValue('PT15M')->end()
			->arrayNode('programs')->isRequired()
				->prototype('array')
					->children()
						->scalarNode('programId')->end()
						->scalarNode('authToken')->end()
					->end()
				->end()
			->end()
		->end();


		// Here you should define the parameters that are allowed to
		// configure your bundle. See the documentation linked above for
		// more information on that topic.

		return $treeBuilder;
	}
}
