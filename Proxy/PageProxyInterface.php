<?php
/**
 * Created by PhpStorm.
 * User: vincenzotrapani
 * Date: 05/08/15
 * Time: 09:30
 */

namespace Amara\Bundle\OneHydraBundle\Proxy;

use Amara\OneHydra\Object\PageObject;

interface PageProxyInterface {

	/**
	 * @param string $pageName
	 * @param string $programId
	 */
	public function getPage($pageName, $programId);

	/**
	 * @param PageObject $pageObject
	 * @param string $programId
	 */
	public function persistPageObject(PageObject $pageObject, $programId);

	/**
	 * @param PageObject $pageObject
	 */
	public function postCreation(PageObject $pageObject);
}