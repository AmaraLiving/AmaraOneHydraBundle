<?php
namespace Amara\Bundle\OneHydraBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpFoundation\Response;
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
		$request = $request->getRequest();
		$pathInfo = $request->getPathInfo();

		if ($oneHydraPage = $this->pageManager->getPage($pathInfo, 'uk')) {
			$pageObject = $oneHydraPage->getPageObject();

			if (in_array($pageObject->getRedirectCode(), [301, 302])) {
				$event->setResponse(new RedirectResponse($pageObject->getRedirectUrl(), $pageObject->getRedirectCode()));
			}
		}
	}
}