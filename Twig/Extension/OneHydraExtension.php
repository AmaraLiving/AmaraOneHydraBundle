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
	 * @param PageManager $pageManager
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
			'oneHydraHeadContent' => new \Twig_Function_Method($this, 'getOneHydraHeadContent')
		];
	}

	/**
	 * @param string $key
	 * @param string $defaultValue
	 * @param string $programId optional
	 * @return string
	 */
	public function getOneHydraHeadContent($key, $defaultValue, $programId = null) {
		$request = $this->requestStack->getCurrentRequest();
		$pathInfo =  $request->getPathInfo();

		if ($oneHydraPage = $this->pageManager->getPage($pathInfo, $programId)) {
			$pageObject = $oneHydraPage->getPageObject();

			$methodName = 'get' . ucwords(strtoLower($key));

			if (method_exists($pageObject, $methodName)) {
				return $pageObject->$methodName();
			}

		} else {
			return $defaultValue;
		}
	}
}