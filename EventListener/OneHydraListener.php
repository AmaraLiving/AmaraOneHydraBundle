<?php
namespace Amara\Bundle\OneHydraBundle\EventListener;

use Amara\Bundle\OneHydraBundle\Strategy\PageNameTransformStrategyInterface;
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

	/** @var PageNameTransformStrategyInterface */
	public $pageNameTransformStrategy;

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
	 * @param PageNameTransformStrategyInterface $pageNameTransformStrategy
	 */
	public function setPageNameTransformStrategy(PageNameTransformStrategyInterface $pageNameTransformStrategy) {
		$this->pageNameTransformStrategy = $pageNameTransformStrategy;
	}

	/**
	 * @param GetResponseEvent $event
	 */
	public function onKernelRequest(GetResponseEvent $event) {

		if ($event->isMasterRequest()) {
			$request = $event->getRequest();

			if (false !== strpos($request->headers->get('accept'), 'text/html')) {

				$pageName = $this->pageNameTransformStrategy->getPageName($request);

				if ($pageEntity = $this->pageManager->getPage($pageName)) {
					$page = $pageEntity->getPageObject();

					$hasRedirectCode = in_array($page->getRedirectCode(), [301, 302]);
					$hasRedirectUrl = (strlen($page->getRedirectUrl()) > 0);

					if ($hasRedirectCode && $hasRedirectUrl) {
						$event->setResponse(new RedirectResponse($page->getRedirectUrl(), $page->getRedirectCode()));
					} else {
						$request->attributes->set($this->pageManager->requestAttributeKey, $pageEntity->getPageName());
					}
				}
			}
		}
	}
}
