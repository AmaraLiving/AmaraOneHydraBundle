<?php

/*
 * This file is part of the AmaraOneHydraBundle package.
 *
 * (c) Amara Living Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Amara\Bundle\OneHydraBundle\Tests\EventListener;

use Amara\Bundle\OneHydraBundle\Entity\OneHydraPage;
use Amara\Bundle\OneHydraBundle\EventListener\OneHydraListener;
use Amara\Bundle\OneHydraBundle\Service\PageManager;
use Amara\OneHydra\Model\Page;
use PHPUnit_Framework_TestCase;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class OneHydraListenerTest extends PHPUnit_Framework_TestCase
{
    /** @var OneHydraListener */
    public $listener;

    /** @var Response */
    public $response;

    /** @var Request */
    public $request;

    public function setUp()
    {
        $this->listener = new OneHydraListener();
        $this->response = new Response();
        $this->request = new Request();
    }

    public function testNoRedirectWithEmptyUrl()
    {
        $pageName = '/blah';
        $redirectCode = 301;
        $redirectUrl = '';

        $page = $this->prophesize(Page::class);
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

        $pageManager = $this->prophesize(PageManager::class);
        $pageManager->getPageByRequest($this->request)->willReturn($pageEntity);
        $this->listener->setPageManager($pageManager->reveal());

        $this->listener->onKernelRequest($event->reveal());
    }

    public function testRedirectWithNonEmptyUrl()
    {
        $pageName = '/blah';
        $redirectCode = 301;
        $redirectUrl = '/';

        $page = $this->prophesize(Page::class);
        $page->getRedirectCode()->willReturn($redirectCode);
        $page->getRedirectUrl()->willReturn($redirectUrl);

        $pageEntity = $this->prophesize(OneHydraPage::class);
        $pageEntity->getPageObject()->willReturn($page);
        $pageEntity->getPageName()->willReturn($pageName);

        $event = $this->prophesize(GetResponseEvent::class);
        $event->getRequest()->willReturn($this->request);
        $event->isMasterRequest()->willReturn(true);
        $event->setResponse(
            Argument::that(
                function ($item) use ($redirectUrl, $redirectCode) {
                    return (
                        $item instanceof RedirectResponse &&
                        $redirectUrl === $item->getTargetUrl() &&
                        $redirectCode === $item->getStatusCode()
                    );
                }
            )
        )->shouldBeCalled();

        $this->request->headers->set('accept', 'text/html');

        $pageManager = $this->prophesize(PageManager::class);
        $pageManager->getPageByRequest($this->request)->willReturn($pageEntity);

        $this->listener->setPageManager($pageManager->reveal());

        $this->listener->onKernelRequest($event->reveal());
    }
}