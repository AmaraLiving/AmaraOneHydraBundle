<?php
namespace Amara\Bundle\OneHydraBundle\Service;

use Amara\Bundle\OneHydraBundle\Proxy\PageProxyInterface;
use Amara\Bundle\OneHydraBundle\Strategy\ProgramSolverStrategyInterface;
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

	/** @var ProgramSolverStrategyInterface */
	private $programIdSolverStrategy;

	/**
	 * @param PageProxyInterface $pageProxy
	 */
	public function setPageProxy(PageProxyInterface $pageProxy) {
		$this->pageProxy = $pageProxy;
	}

	/**
	 * @param ProgramSolverStrategyInterface $programIdSolverStrategy
	 */
	public function setProgramSolverStrategy(ProgramSolverStrategyInterface $programIdSolverStrategy) {
		$this->programIdSolverStrategy = $programIdSolverStrategy;
	}	

	/**
	 * @param PageObject $pageObject
	 * @param string $programId
	 */
	public function addPage(PageObject $pageObject, $programId = null) {

		// Remove the page if already exists (we are getting updated infos)
		$this->removeIfExists($pageObject->getPageName(), $this->getProgramId($programId));

		// Page creation
		$this->pageProxy->addPage($pageObject, $programId);

		// Post creation operations
		$this->pageProxy->postCreation($pageObject, $programId);

	}

	/**
	 * @param string $pageName
	 * @param string $programId
	 */
	public function removeIfExists($pageName, $programId = null) {
		$this->pageProxy->removeIfExists($pageName, $this->getProgramId($programId));
	}

	/**
	 * @param string $pageName
	 * @param string $programId
	 * @return array|bool
	 */
	public function getPage($pageName, $programId = null) {
		return $this->pageProxy->getPage($pageName, $this->getProgramId($programId));
	}

	/**
	 * @param string $programId
	 * @return string
	 */
	public function getProgramId($programId) {
		if (is_null($programId)) {
			if ($this->programIdSolverStrategy instanceof ProgramSolverStrategyInterface) {
				return $this->programIdSolverStrategy->getProgramId();
			}
		} else {
			return $programId;
		}
	}

}