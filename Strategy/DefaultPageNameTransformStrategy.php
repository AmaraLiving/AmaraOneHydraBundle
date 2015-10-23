<?php
namespace Amara\Bundle\OneHydraBundle\Strategy;
 
class DefaultPageNameTransformStrategy implements PageNameTransformStrategyInterface {

	/**
	 * By default, the PageName = $url
	 *
	 * @param string $url
	 * @return string
	 */
	public function getPageName($url) {
		return $url;
	}
}
