<?php

namespace Amara\Bundle\OneHydraBundle;

use Amara\Bundle\OneHydraBundle\DependencyInjection\Compiler\ProxyPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AmaraOneHydraBundle extends Bundle
{
	public function build(ContainerBuilder $container) {
		parent::build($container);

		$container->addCompilerPass(new ProxyPass());
	}
}
