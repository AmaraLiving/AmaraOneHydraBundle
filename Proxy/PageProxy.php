<?php

namespace Amara\Bundle\OneHydraBundle\Proxy;

use Amara\Bundle\OneHydraBundle\Entity\OneHydraPage;
use Amara\Bundle\OneHydraBundle\Repository\OneHydraPageRepository;
use Amara\OneHydra\Object\PageObject;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;

class PageProxy implements PageProxyInterface {

	/** @var EntityManager */
	public $entityManager;

	/** @var Container */
	public $container;

	/**
	 * @param EntityManager $entityManager
	 */
	public function setEntityManager(EntityManager $entityManager) {
		$this->entityManager = $entityManager;
	}

	/**
	 * @param Container $container
	 */
	public function setContainer(Container $container) {
		$this->container = $container;
	}

	/**
	 * @param string $pageName
	 * @param string $programId
	 * @return OneHydraPage
	 */
	public function getPage($pageName, $programId) {
		return $this->getPageFromDB($pageName, $programId);
	}

	/**
	 * @param string $pageName
	 * @param string $programId
	 * @return OneHydraPage
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
	}

	/**
	 * @param PageObject $pageObject
	 * @param string $programId
	 */
	public function persistPageObject(PageObject $pageObject, $programId) {
		$ohPage = $this->getOneHydraPage();
		$ohPage->setPageName($pageObject->getPageName());
		$ohPage->setPageObject($pageObject);
		$ohPage->setProgramId($programId);
		$ohPage->setCreatedAt(new \DateTime());

		$this->entityManager->persist($ohPage);
		$this->entityManager->flush();
	}

	/**
	 * @return OneHydraPage
	 */
	public function getOneHydraPage() {
		return $this->container->get('onehydra_page_entity');
	}

	/**
	 * @param PageObject $pageObject
	 * @param string $programId
	 */
	public function postCreation(PageObject $pageObject, $programId) {

	}
}