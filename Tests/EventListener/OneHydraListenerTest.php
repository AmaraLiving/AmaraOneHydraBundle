<?php
/**
 * Created by PhpStorm.
 * User: vincenzotrapani
 * Date: 13/08/15
 * Time: 15:07
 */
namespace Amara\Bundle\OneHydraBundle\Tests\EventListner;

use Amara\Bundle\OneHydraBundle\Entity\OneHydraPage;
use Amara\Bundle\OneHydraBundle\EventListener\OneHydraListener;
use Amara\Bundle\OneHydraBundle\Service\PageManager;
use Amara\Bundle\OneHydraBundle\Strategy\PageNameTransformStrategyInterface;
use Amara\OneHydra\Object\PageObject;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

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

	public function testNoRedirectWithEmptyUrl() {
		$pageName = '/blah';
		$redirectCode = 301;
		$redirectUrl = '';

		$page = $this->prophesize(PageObject::class);
		$page->getRedirectCode()->willReturn($redirectCode);
		$page->getRedirectUrl()->willReturn($redirectUrl);

		$pageEntity = $this->prophesize(OneHydraPage::class);
		$pageEntity->getPageObject()->willReturn($page);
		$pageEntity->getPageName()->willReturn($pageName);

		$event = $this->prophesize(GetResponseEvent::class);
		$event->getRequest()->willReturn($this->request);
		$event->isMasterRequest()->willReturn(true);
		$event->setResponse(Argument::any())->shouldNotBeCalled();

		$this->request->headers->set('accept', 'text/html');

		$pageNameTransformStrategy = $this->prophesize(PageNameTransformStrategyInterface::class);
		$pageNameTransformStrategy->getPageName($this->request)->willReturn($pageName);

		$pageManager = $this->prophesize(PageManager::class);
		$pageManager->getPage($pageName)->willReturn($pageEntity);

		$this->listener->setPageManager($pageManager->reveal());
		$this->listener->setPageNameTransformStrategy($pageNameTransformStrategy->reveal());

		$this->listener->onKernelRequest($event->reveal());

		$this->assertEquals($pageName, $this->request->attributes->get('_one_hydra_name'), "Page name attribute was set on request");
	}

	public function testRedirectWithNonEmptyUrl() {
		$pageName = '/blah';
		$redirectCode = 301;
		$redirectUrl = '/';

		$page = $this->prophesize(PageObject::class);
		$page->getRedirectCode()->willReturn($redirectCode);
		$page->getRedirectUrl()->willReturn($redirectUrl);

		$pageEntity = $this->prophesize(OneHydraPage::class);
		$pageEntity->getPageObject()->willReturn($page);
		$pageEntity->getPageName()->willReturn($pageName);

		$event = $this->prophesize(GetResponseEvent::class);
		$event->getRequest()->willReturn($this->request);
		$event->isMasterRequest()->willReturn(true);
		$event->setResponse(Argument::that(function ($item) use ($redirectUrl, $redirectCode) {
			return (
				$item instanceof RedirectResponse &&
				$redirectUrl === $item->getTargetUrl() &&
				$redirectCode === $item->getStatusCode()
			);
		}))->shouldBeCalled();

		$this->request->headers->set('accept', 'text/html');

		$pageNameTransformStrategy = $this->prophesize(PageNameTransformStrategyInterface::class);
		$pageNameTransformStrategy->getPageName($this->request)->willReturn($pageName);

		$pageManager = $this->prophesize(PageManager::class);
		$pageManager->getPage($pageName)->willReturn($pageEntity);

		$this->listener->setPageManager($pageManager->reveal());
		$this->listener->setPageNameTransformStrategy($pageNameTransformStrategy->reveal());

		$this->listener->onKernelRequest($event->reveal());

		$this->assertNotEquals($pageName, $this->request->attributes->get('_one_hydra_name'), "Page name attribute was set on request");
	}
}