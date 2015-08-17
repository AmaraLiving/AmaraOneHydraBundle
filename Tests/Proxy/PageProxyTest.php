<?php
namespace Amara\Bundle\OneHydraBundle\Tests\Proxy;

use Amara\Bundle\OneHydraBundle\Proxy\PageProxy;
use Amara\OneHydra\Object\PageObject;

/**
 * Created by PhpStorm.
 * User: vincenzotrapani
 * Date: 14/08/15
 * Time: 10:24
 */

class PageProxyTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @cover PageProxy::addPage
	 * @cover PageProxy::removeIfExists
	 */
	public function testPageProxyCreatesANewOneHydraPage() {

		$pageProxy = new PageProxy();
		$pageProxy->setContainer($this->getContainerMock());
		$pageProxy->setEntityManager($this->getEntityManagerMock());

		$pageObject = new PageObject();
		$pageObject->setPageName('http:\\www.foo.bar');

		$pageProxy->removeIfExists('http:\\www.foo.bar', 'uk');

		$pageProxy->addPage($pageObject, 'uk');

		$pageProxy->removeIfExists('http:\\www.foo.bar', 'uk');
	}


	/**
	 * @cover PageProxy::getPage
	 * @cover PageProxy::getPageFromDB
	 */
	public function testGetPageReturnsTheRightObject() {


		$pageObject = new PageObject();
		$pageObject->setPageName('http:\\www.foo.bar');

		$pageEntity = $this->getOneHydraPageSpy(true);
		$pageEntity->expects($this->any())
				   ->method('getPageObject')
			       ->willReturn($pageObject);

		$pageProxy = new PageProxy();
		$pageProxy->setContainer($this->getContainerMock(true));
		$pageProxy->setEntityManager($this->getEntityManagerMock(true, $pageEntity));

		$oneHydraPage = $pageProxy->getPage('http:\\www.foo.bar', 'uk');


		$this->assertEquals($oneHydraPage->getPageObject(), $pageObject);
	}


	public function getEntityManagerMock($returnObject = false, $object = null) {
		$entityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
			                  ->disableOriginalConstructor()
			                  ->setMethods(['getRepository', 'persist', 'flush', 'remove'])
			                  ->getMock();

		$entityManager->expects($this->atLeastOnce())
			          ->method('getRepository')
			          ->with('AmaraOneHydraBundle:OneHydraPage')
			          ->willReturn($this->getOneHydraPageRepositoryMock($returnObject, $object));

		$entityManager->expects($this->any())
			          ->method('persist');

		$entityManager->expects($this->any())
			          ->method('flush');

		return $entityManager;
	}

	public function getOneHydraPageRepositoryMock($returnObject = false, $object = null) {
		$repository = $this->getMockBuilder('\Amara\Bundle\OneHydraBundle\Repository\OneHydraPageRepository')
			               ->disableOriginalConstructor()
			               ->setMethods(['findOneByPageName'])
			               ->getMock();

		if (false === $returnObject) {
			$repository->expects($this->any())
				->method('findOneByPageName')
				->will($this->onConsecutiveCalls(null , true));
		} else {
			$repository->expects($this->any())
				->method('findOneByPageName')
				->willReturn($object);
		}

		return $repository;
	}

	public function getContainerMock($noSpy = false) {
		$container = $this->getMockBuilder('\Symfony\Component\DependencyInjection\Container')
			              ->disableOriginalConstructor()
			              ->setMethods(['get'])
			              ->getMock();

		$container->expects($this->any())
			      ->method('get')
			      ->with('onehydra_page_entity')
			      ->willReturn($this->getOneHydraPageSpy($noSpy));

		return $container;
	}

	public function getOneHydraPageSpy($noSpy = false) {
		$page = $this->getMockBuilder('\Amara\Bundle\OneHydraBundle\Entity\OneHydraPage')
					 ->setMethods(['setPageName', 'setPageObject', 'setProgramId', 'setCreatedAt', 'getPageObject'])
			         ->getMock();


		if (!$noSpy) {
			$page->expects($this->once())
				->method('setPageName');

			$page->expects($this->once())
				->method('setPageObject');

			$page->expects($this->once())
				->method('setProgramId');

			$page->expects($this->once())
				->method('setCreatedAt');
		}


		return $page;
	}
}