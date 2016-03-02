<?php

namespace Amara\Bundle\OneHydraBundle\Twig\Extension;


use Amara\Bundle\OneHydraBundle\Service\PageManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig_SimpleFunction;

class OneHydraExtension extends \Twig_Extension {

	/**
	 * @var Request
	 */
	private $request;

	/**
	 * @var PageManager
	 */
	private $pageManager;


	/**
	 * @param RequestStack $request
	 */
	public function setRequest(RequestStack $request) {
		$this->request = $request->getCurrentRequest();
	}


	/**
	 * @param PageManager $pageManager
	 */
	public function setPageManager(PageManager $pageManager) {
		$this->pageManager = $pageManager;
	}

	/**
	 * @param Request $request
	 * @return bool
	 */
	public function isSuggested($request = null) {

		if (is_null($request)) {
			$request = $this->request;
		}

		if ($page = $this->pageManager->getPageByRequest($request)) {
			$pageObject = $page->getPageObject();

			return $pageObject->isSuggested();
		}

		return false;
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
			'oneHydraHeadContent' => new Twig_SimpleFunction('oneHydraHeadContent', [$this, 'getOneHydraHeadContent']),
			'oneHydraIsSuggestedPage' => new Twig_SimpleFunction('oneHydraIsSuggestedPage', [$this, 'isSuggested'])
		];
	}

	/**
	 * @param string $key
	 * @param string $defaultValue
	 * @param Request $request
	 * @return string
	 */
	public function getOneHydraHeadContent($key, $defaultValue, $request = null) {

		if (is_null($request)) {
			$request = $this->request;
		}

		if ($page = $this->pageManager->getPageByRequest($request)) {

			if ($pageObject = $page->getPageObject()) {

				$methodName = 'get' . ucwords(strtolower($key));

				if (method_exists($pageObject, $methodName)) {
					$value = $pageObject->$methodName();

					return (!is_null($value) ? ($value) : $defaultValue);
				}

			}
		} else {
			return $defaultValue;
		}
	}
}