<?php
/**
 * Created by PhpStorm.
 * User: vincenzotrapani
 * Date: 05/08/15
 * Time: 09:28
 */
namespace Amara\Bundle\OneHydraBundle\Proxy;

use Amara\Bundle\OneHydraBundle\Entity\OneHydraPage;
use Amara\Bundle\OneHydraBundle\Repository\OneHydraPageRepository;
use Amara\OneHydra\Object\PageObject;
use Doctrine\ORM\EntityManager;

class PageProxy implements PageProxyInterface {

	/** @var EntityManager */
	public $entityManager;

	/**
	 * @param EntityManager $entityManager
	 */
	public function setEntityManager($entityManager) {
		$this->entityManager = $entityManager;
	}

	/**
	 * @param string $pageName
	 * @param string $programId
	 * @return mixed
	 */
	public function getPage($pageName, $programId) {
		return $this->getPageFromDB($pageName, $programId);
	}

	/**
	 * @param string $pageName
	 * @param string $programId
	 * @return mixed
	 */
	protected function getPageFromDB($pageName, $programId) {
		return $this->getOneHydraPageRepository()->findOneByPageName($pageName, $programId);
	}

	/**
	 * @return OneHydraPageRepository
	 */
	private function getOneHydraPageRepository() {
		return $this->entityManager->getRepository('AmaraOneHydraBundle:OneHydraPage');
	}

	/**
	 * @param string $pageName
	 * @param string $programId
	 */
	public function removeIfExists($pageName, $programId) {

		$page = $this->getOneHydraPageRepository()->findOneByPageName($pageName, $programId);

		if ($page) {
			$this->entityManager->remove($page);
		}
	}

	/**
	 * @param PageObject $pageObject
	 * @param string $programId
	 */
	public function addPage(PageObject $pageObject, $programId) {
		// Persist the object
		$this->persistPageObject($pageObject, $programId);

		// Post creation operations
		$this->postCreation($pageObject);
	}

	/**
	 * @param PageObject $pageObject
	 * @param string $programId
	 */
	public function persistPageObject(PageObject $pageObject, $programId) {
		$ohPage = new OneHydraPage();
		$ohPage->setPageName($pageObject->getPageName());
		$ohPage->setPageObject($pageObject);
		$ohPage->setProgramId($programId);
		$ohPage->setCreatedAt(new \DateTime());

		$this->entityManager->persist($ohPage);
		$this->entityManager->flush();
	}

	/**
	 * @param PageObject $pageObject
	 */
	public function postCreation(PageObject $pageObject) {

	}
}