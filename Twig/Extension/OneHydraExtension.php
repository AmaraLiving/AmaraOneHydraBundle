<?php

namespace Amara\Bundle\OneHydraBundle\Twig\Extension;

use Amara\Bundle\OneHydraBundle\Service\PageManager;
use Symfony\Component\HttpFoundation\RequestStack;

class OneHydraExtension extends \Twig_Extension {
	
	/**
	 * @var PageManager
	 */
	private $pageManager;

	/**
	 * @var RequestStack
	 */
	private $requestStack;

	/**
	 * @var RequestStack
	 */
	public function __construct(RequestStack $requestStack) {
		$this->requestStack = $requestStack;
	}

	/**
	 * @param PageManager
	 */
	public function setPageManager($pageManager) {
		$this->pageManager = $pageManager;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'onehydra_extension';
	}

	/**
	 * @return array
	 */
	public function getFunctions() {
		return [
			'oneHydraHeadContent' => \Twig_Function_Method($this, 'getOneHydraHeadContent')
		];
	}

	/**
	 * @param string $key
	 * @param string $defaultValue
	 * @return string
	 */
	private function getOneHydraHeadContent($key, $programId, $defaultValue) {
		$request = $this->requestStack->getCurrentRequest();
		$pathInfo =  $request->getPathInfo();

		if ($oneHydraPage = $this->pageManager->getPage($pathInfo, $pathInfo)) {
			$headContent = $oneHydraPage->getHeadContent();

			if (isset($headContent->{$key})) {
				return $headContent->{$key};
			}

		} else {
			return $defaultValue;
		}
	}
}