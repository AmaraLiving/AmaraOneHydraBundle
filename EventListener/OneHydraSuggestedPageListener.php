<?php
namespace Amara\Bundle\OneHydraBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Amara\Bundle\OneHydraBundle\Service\PageManager;

class OneHydraSuggestedPageListener {

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
	 * @param FilterResponseEvent $event
	 */
	public function onKernelResponse(FilterResponseEvent $event) {
		$request = $event->getRequest();
		$uri = $request->getRequestUri();

		if ($oneHydraPage = $this->pageManager->getPage($uri)) {
			$pageObject = $oneHydraPage->getPageObject();
			
			if ($pageObject->isSuggested()) {
				$event->getResponse()->headers->set('X-OH', 'Suggested');
			}
		}
	}
}
