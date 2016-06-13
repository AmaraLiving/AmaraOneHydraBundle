<?php

/*
 * This file is part of the AmaraOneHydraBundle package.
 *
 * (c) Amara Living Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Amara\Bundle\OneHydraBundle\EventListener;

use Amara\Bundle\OneHydraBundle\Entity\OneHydraPageInterface;
use Amara\Bundle\OneHydraBundle\Service\PageManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * OneHydraListener
 */
class OneHydraListener
{
    /**
     * @var PageManager
     */
    private $pageManager;

    /**
     * @param PageManager $pageManager
     */
    public function setPageManager($pageManager)
    {
        $this->pageManager = $pageManager;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (false === strpos($request->headers->get('accept'), 'text/html')) {
            return;
        }

        $pageEntity = $this->pageManager->getPageByRequest($request);

        if (!$pageEntity instanceof OneHydraPageInterface) {
            return;
        }

        $page = $pageEntity->getPageObject();

        $redirectCode = $page->getRedirectCode();
        $redirectUrl = $page->getRedirectUrl();

        $hasRedirectCode = in_array($redirectCode, [301, 302]);
        $hasRedirectUrl = (strlen($redirectUrl) > 0);

        if ($hasRedirectCode && $hasRedirectUrl) {
            $event->setResponse(new RedirectResponse($redirectUrl, $redirectCode));
        }
    }
}
