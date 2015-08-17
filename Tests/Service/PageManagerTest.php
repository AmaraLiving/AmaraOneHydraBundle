<?php
/**
 * Created by PhpStorm.
 * User: vincenzotrapani
 * Date: 17/08/15
 * Time: 09:17
 */

namespace Amara\Bundle\OneHydraBundle\Tests\Service;

use Amara\Bundle\OneHydraBundle\Service\PageManager;
use Amara\OneHydra\Object\PageObject;

class PageManagerTest extends \PHPUnit_Framework_TestCase{


	/**
	 * @cover PageManager::addPage
	 */
	public function testAddPageMethodCallsAllTheInternalProxyMethods() {
		$proxy = $this->getProxyMock();

		$proxy->expects($this->once())
			  ->method('postCreation')
			  ->with(
				  $this->isInstanceOf('\Amara\OneHydra\Object\PageObject')
			  );

		$proxy->expects($this->once())
			  ->method('removeIfExists');

		$proxy->expects($this->once())
			  ->method('addPage')
			  ->with(
				    $this->isInstanceOf('\Amara\OneHydra\Object\PageObject'),
			        $this->equalTo('uk')
			  );

		$pageObject = new PageObject();

		$pageManager = new PageManager();
		$pageManager->setPageProxy($proxy);

		$pageManager->addPage($pageObject, 'uk');
	}

	/**
	 * @cover PageManager::getPage
	 * @cover PageManager::getProgramId
	 */
	public function testGetPageCallsTheProxy() {

		$proxy = $this->getProxyWithGetPageMethodConfigured();

		$pageManager = new PageManager();
		$pageManager->setPageProxy($proxy);

		$this->assertEquals('uk', $pageManager->getProgramId('uk'));

		$pageManager->getPage('/testpagename', 'uk');

	}


	/**
	 * @cover PageManager::getProgramId
	 */
	public function testPageManagerUsesProgramSolverWhenNullProgramIdIsGivenOrUsesTheDefaultValue() {
		$proxy = $this->getProxyWithGetPageMethodConfigured();

		$pageManager = new PageManager();
		$pageManager->setPageProxy($proxy);
		$pageManager->setProgramSolverStrategy($this->getProgramSolverMock());


		$this->assertEquals('uk', $pageManager->getProgramId(null));
		$this->assertEquals('fr', $pageManager->getProgramId('fr'));

		$pageManager->getPage('/testpagename');
	}


	public function getProgramSolverMock() {
		$programSolver = $this->getMockBuilder('\Amara\Bundle\OneHydraBundle\Strategy\ProgramSolverStrategyInterface')
			                  ->setMethods(['getProgramId'])
			                  ->getMock();

		$programSolver->expects($this->any())
					  ->method('getProgramId')
			          ->willReturn('uk');

		return $programSolver;
	}

	public function getProxyWithGetPageMethodConfigured() {
		$proxy = $this->getProxyMock();

		$proxy->expects($this->once())
			->method('getPage')
			->with(
				$this->equalTo('/testpagename'),
				$this->equalTo('uk')
			);

		return $proxy;
	}

	public function getProxyMock() {
		$proxy = $this->getMockBuilder('\Amara\Bundle\OneHydraBundle\Proxy\PageProxyInterface')
					  ->setMethods(['addPage', 'getPage', 'persistPageObject', 'postCreation', 'removeIfExists'])
					  ->getMock();

		return $proxy;
	}
}