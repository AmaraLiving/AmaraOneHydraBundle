<?php

/*
 * This file is part of the AmaraOneHydraBundle package.
 *
 * (c) Amara Living Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Amara\Bundle\OneHydraBundle\Strategy;

use Amara\OneHydra\Model\PageInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * PageTransformStrategyInterface
 */
interface PageTransformStrategyInterface
{
    /**
     * @param PageInterface $page
     * @return PageInterface
     */
    public function transformPageForStorage(PageInterface $page);

    /**
     * Get the page name to use on our system from the OneHydra page
     *
     * @param PageInterface $page
     * @return string
     */
    public function getPageNameForStorage(PageInterface $page);

    /**
     * Get the page name to lookup the page on our system for the given request
     *
     * @param Request $request
     * @return string
     */
    public function getLookupPageName(Request $request);

    /**
     * Get the program id to lookup the page on our system for the given request
     *
     * @param Request $request
     * @return string
     */
    public function getLookupProgramId(Request $request);

    /**
     * Transform a url from a OneHydra page for display
     *
     * @param string $url
     * @return string
     */
    public function transformDisplayUrl($url);
}