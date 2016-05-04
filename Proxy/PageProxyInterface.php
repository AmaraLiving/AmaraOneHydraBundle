<?php

namespace Amara\Bundle\OneHydraBundle\Proxy;

use Amara\Bundle\OneHydraBundle\Entity\OneHydraPage;
use Amara\OneHydra\Object\PageObject;

interface PageProxyInterface {

	/**
	 * @param string $pageName
	 * @param string $programId
	 * @return OneHydraPage
	 */
	public function getPage($pageName, $programId);

	/**
	 * @param PageObject $pageObject
	 * @param string $programId
	 */
	public function persistPageObject(PageObject $pageObject, $programId);

	/**
	 * @param PageObject $pageObject
	 * @param string $programId
	 */
	public function postCreation(PageObject $pageObject, $programId);
}