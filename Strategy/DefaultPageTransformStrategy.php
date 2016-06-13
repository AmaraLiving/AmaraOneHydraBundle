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
 * DefaultPageTransformStrategy
 */
class DefaultPageTransformStrategy implements PageTransformStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function getLookupPageName(Request $request)
    {
        return $request->getPathInfo();
    }

    /**
     * {@inheritdoc}
     */
    public function getLookupProgramId(Request $request)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getPageNameForStorage(PageInterface $page)
    {
        return $page->getPageUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function transformDisplayUrl($url)
    {
        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function transformPageForStorage(PageInterface $page)
    {
        return $page;
    }
}
