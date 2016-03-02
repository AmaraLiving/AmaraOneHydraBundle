<?php
namespace Amara\Bundle\OneHydraBundle\Service;

use Amara\Bundle\OneHydraBundle\Entity\OneHydraPage;
use Amara\Bundle\OneHydraBundle\Proxy\PageProxyInterface;
use Amara\Bundle\OneHydraBundle\Strategy\ProgramSolverStrategyInterface;
use Amara\OneHydra\Object\PageObject;
use Symfony\Component\HttpFoundation\Request;


class PageManager {

	/** @var PageProxyInterface */
	private $pageProxy;

	/** @var ProgramSolverStrategyInterface */
	private $programIdSolverStrategy;

	/** @var array */
	private $cache = [];

	/** @var string */
	public $requestAttributeKey = '_one_hydra_name';

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
	 * @return OneHydraPage|bool
	 */
	public function getPage($pageName, $programId = null) {

		if (in_array($pageName, $this->cache)) {
			return $this->cache[$pageName];
		}

		$page = $this->pageProxy->getPage($pageName, $this->getProgramId($programId));

		if ($page) {
			$this->cache[$pageName] = $page;
		}

		return $page;
	}

	/**
	 * @param Request $request
	 * @param string $programId
	 * @return OneHydraPage|bool
	 */
	public function getPageByRequest(Request $request, $programId = null) {
		if ($pageName = $request->attributes->get($this->requestAttributeKey, null)) {
			return $this->getPage($pageName, $programId);
		}

		return false;
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