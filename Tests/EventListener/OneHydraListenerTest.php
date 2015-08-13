<?php
/**
 * Created by PhpStorm.
 * User: vincenzotrapani
 * Date: 13/08/15
 * Time: 15:07
 */
namespace Amara\Bundle\OneHydraBundle\Tests\EventListner;

use Amara\Bundle\OneHydraBundle\EventListener\OneHydraListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OneHydraListenerTest extends \PHPUnit_Framework_TestCase {

	/** @var OneHydraListener */
	public $listener;

	/** @var Response */
	public $response;

	/** @var Request */
	public $request;

	public function setUp() {
		$this->listener = new OneHydraListener();
		$this->response = new Response();
		$this->request = new Request();
	}


	public function testRedirectIsCatched() {
		$pageEntity = $this->getPageRedirectMock();
		$pageManager = $this->getPageManagerMock($pageEntity);

		$this->listener->setPageManager($pageManager);
		$this->listener->onKernelRequest($this->getEventMock());
	}

	public function testNoRedirect() {
		$pageEntity = $this->getPage200Mock();
		$pageManager = $this->getPageManagerMock($pageEntity);
		$currenPageState = $this->getCurretPageStateMock();

		$this->listener->setPageManager($pageManager);
		$this->listener->setCurrentPageState($currenPageState);
		$this->listener->onKernelRequest($this->getEventMock());
	}

	private function getEventMock() {
		$event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
			          ->disableOriginalConstructor()
			          ->setMethods(['getRequest', 'setResponse'])
			          ->getMock();

		$event->expects($this->once())
			  ->method('getRequest')
			  ->willReturn($this->request);

		return $event;
	}

	private function getCurretPageStateMock() {
		$currentPageState = $this->getMockBuilder('\Amara\Bundle\OneHydra\State\CurrentPageState')
			                     ->setMethods(['setPage'])
								 ->getMock();

		$currentPageState->expects($this->once())
						 ->method('setPage');

		return $currentPageState;
	}

	public function getPageManagerMock($pageEntity) {
		$pageManager = $this->getMockBuilder('\Amara\Bundle\OneHydraBundle\Service\PageManager')
							->setMethods(['getPage'])
							->getMock();

		$pageManager->expects($this->once())
				    ->method('getPage')
					->willReturn($pageEntity);

		return $pageManager;
	}


	private function getPage200Mock() {

		$pageObject = $this->getMockBuilder('\Amara\OneHydra\Object\PageObject')
			->setMethods(['getRedirectCode'])
			->getMock();

		$pageObject->expects($this->once())
			->method('getRedirectCode')
			->willReturn(200);

		$pageObject->expects($this->never())
			->method('getRedirectUrl');

		return $this->getPageEntity($pageObject);
	}


	private function getPageRedirectMock() {

		$pageObject = $this->getMockBuilder('\Amara\OneHydra\Object\PageObject')
					 ->setMethods(['getRedirectCode', 'getRedirectUrl'])
			         ->getMock();

		$pageObject->expects($this->exactly(2))
					->method('getRedirectCode')
					->willReturn(301);

		$pageObject->expects($this->once())
					->method('getRedirectUrl')
					->willReturn('http://foo.bar');

		return $this->getPageEntity($pageObject);
	}

	private function getPageEntity($pageObject) {
		$pageEntity = $this->getMockBuilder('\stdClass')
						->setMethods(['getPageObject'])
						->getMock();

		$pageEntity->expects($this->any())
					->method('getPageObject')
					->willReturn($pageObject);

		return $pageEntity;
	}
}