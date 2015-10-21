<?php
namespace Amara\Bundle\OneHydraBundle\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Amara\Bundle\OneHydraBundle\Service\PageManager;
use Amara\Bundle\OneHydraBundle\State\CurrentPageState;

class OneHydraListener {

	/**
	 * @var PageManager
	 */
	private $pageManager;

	/**
	 * @var CurrentPageState
	 */
	private $currentPageState;

	/**
	 * @param PageManager $pageManager
	 */
	public function setPageManager($pageManager) {
		$this->pageManager = $pageManager;
	}

	/**
	 * @param CurrentPageState $currentPageState
	 */
	public function setCurrentPageState($currentPageState) {
		$this->currentPageState = $currentPageState;
	}

	/**
	 * @param GetResponseEvent $event
	 */
	public function onKernelRequest(GetResponseEvent $event) {

		if ($event->isMasterRequest()) {
			$request = $event->getRequest();
			$uri = $request->getUri();

			if (false !== strpos($request->headers->get('accept'), 'text/html')) {

				if ($oneHydraPage = $this->pageManager->getPage($uri)) {
					$pageObject = $oneHydraPage->getPageObject();


					if (in_array($pageObject->getRedirectCode(), [301, 302])) {
						$event->setResponse(new RedirectResponse($pageObject->getRedirectUrl(), $pageObject->getRedirectCode()));
					} else {
						$request->attributes->set($this->pageManager->requestAttributeKey, $oneHydraPage->getPageName());
					}
				}
			}
		}
	}
}
