<?php

namespace Amara\Bundle\OneHydraBundle\Twig\Extension;


use Amara\Bundle\OneHydraBundle\State\CurrentPageState;

class OneHydraExtension extends \Twig_Extension {

	/**
	 * @var CurrentPageState
	 */
	private $currentPageState;


	/**
	 * @param CurrentPageState $currentPageState
	 */
	public function setCurrentPageState($currentPageState) {
		$this->currentPageState = $currentPageState;
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
	 * @return string
	 */
	public function getOneHydraHeadContent($key, $defaultValue) {

		if ($pageObject = $this->currentPageState->getPage()) {

			$methodName = 'get' . ucwords(strtolower($key));

			if (method_exists($pageObject, $methodName)) {
				$value = $pageObject->$methodName();

				return (!is_null($value) ? ($value) : $defaultValue);
			}

		} else {
			return $defaultValue;
		}
	}
}