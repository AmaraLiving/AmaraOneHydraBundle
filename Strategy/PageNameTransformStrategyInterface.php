<?php

namespace Amara\Bundle\OneHydraBundle\Strategy;

interface PageNameTransformStrategyInterface {

 	/**
 	 * A custom logic to transform the url got from onehydra
 	 * in one more consistent for the final system
 	 */ 
	public function getPageName($url);
}