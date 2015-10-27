<?php

namespace Amara\Bundle\OneHydraBundle\Strategy;

use Symfony\Component\HttpFoundation\Request;

interface PageNameTransformStrategyInterface {

	/**
	 * A custom logic to transform the url got from onehydra
	 * in one more consistent for the final system
	 * @param Request $request
	 * @return
	 */
	public function getPageName(Request $request);
}