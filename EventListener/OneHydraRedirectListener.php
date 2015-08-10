<?php
namespace Amara\Bundle\OneHydraBundle\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Amara\Bundle\OneHydraBundle\Service\PageManager;

class OneHydraRedirectListener {

	/**
	 * @var PageManager
	 */
	private $pageManager;

	/**
	 * @param PageManager $pageManager
	 */
	public function setPageManager($pageManager) {
		$this->pageManager = $pageManager;
	}

	/**
	 * @param GetResponseEvent $event
	 */
	public function onKernelRequest(GetResponseEvent $event) {
		$request = $event->getRequest();
		$pathInfo = $request->getPathInfo();

		if ($oneHydraPage = $this->pageManager->getPage($pathInfo)) {
			$pageObject = $oneHydraPage->getPageObject();
			
			if (in_array($pageObject->getRedirectCode(), [301, 302])) {
				$event->setResponse(new RedirectResponse($pageObject->getRedirectUrl(), $pageObject->getRedirectCode()));
			}
		}
	}
}
