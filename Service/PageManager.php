<?php
namespace Amara\Bundle\OneHydraBundle\Service;

use Amara\Bundle\OneHydraBundle\Proxy\PageProxyInterface;
use Amara\OneHydra\Object\PageObject;


/**
 * Created by PhpStorm.
 * User: vincenzotrapani
 * Date: 13/07/15
 * Time: 10:31
 */
class PageManager {

	/** @var PageProxyInterface */
	private $pageProxy;


	/**
	 * @param PageProxyInterface $pageProxy
	 */
	public function setPageProxy(PageProxyInterface $pageProxy) {
		$this->pageProxy = $pageProxy;
	}

	/**
	 * @param PageObject $pageObject
	 * @param string $programId
	 */
	public function addPage(PageObject $pageObject, $programId) {

		// Remove the page if already exists (we are getting updated infos)
		$this->removeIfExists($pageObject->getPageName(), $programId);

		// Page creation
		$this->pageProxy->addPage($pageObject, $programId);
	}

	/**
	 * @param string $pageName
	 * @param string $programId
	 */
	public function removeIfExists($pageName, $programId) {
		$this->pageProxy->removeIfExists($pageName, $programId);
	}

	/**
	 * @param string $pageName
	 * @param string $programId
	 * @return array|bool
	 */
	public function getPage($pageName, $programId) {
		return $this->pageProxy->getPage($pageName, $programId);
	}
}