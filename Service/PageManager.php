<?php

/*
 * This file is part of the AmaraOneHydraBundle package.
 *
 * (c) Amara Living Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Amara\Bundle\OneHydraBundle\Service;

use Amara\Bundle\OneHydraBundle\Entity\OneHydraPageInterface;
use Amara\Bundle\OneHydraBundle\Storage\PageStorageInterface;
use Amara\Bundle\OneHydraBundle\Strategy\PageTransformStrategyInterface;
use Amara\OneHydra\Model\PageInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * PageManager
 */
class PageManager
{
    /** @var PageStorageInterface */
    private $pageStorage;

    /** @var PageTransformStrategyInterface */
    public $pageTransformStrategy;

    /** @var array */
    private $cache = [];

    /** @var string */
    CONST REQUEST_KEY_PAGE_NAME = '_one_hydra_name';
    CONST REQUEST_KEY_PROGRAM = '_one_hydra_program';

    /**
     * @param PageStorageInterface $pageStorage
     */
    public function setPageStorage(PageStorageInterface $pageStorage)
    {
        $this->pageStorage = $pageStorage;
    }

    /**
     * @param PageTransformStrategyInterface $pageNameTransformStrategy
     */
    public function setPageTransformStrategy(PageTransformStrategyInterface $pageNameTransformStrategy)
    {
        $this->pageTransformStrategy = $pageNameTransformStrategy;
    }

    /**
     * Create/update our system with this Page from OneHydra
     *
     * @param PageInterface $page
     * @param string $programId
     */
    public function addPage(PageInterface $page, $programId)
    {
        // Transform the raw page if we want to before persisting it
        $page = $this->pageTransformStrategy->transformPageForStorage($page);

        // Get the name to use for persisting the page
        $pageName = $this->pageTransformStrategy->getPageNameForStorage($page);

        // Save the page!
        $this->pageStorage->addPage($page, $pageName, $programId);
    }

    /**
     * Load our OneHydra page entity
     *
     * @todo Rename to findPageEntity?
     * @param string $pageName
     * @param string $programId
     * @return OneHydraPageInterface|null
     */
    public function getPage($pageName, $programId)
    {
        $localCacheKey = $programId.'--'.$pageName;

        if (array_key_exists($localCacheKey, $this->cache)) {
            return $this->cache[$localCacheKey];
        }

        $page = $this->pageStorage->getPageEntity($pageName, $programId);

        if ($page) {
            $this->cache[$localCacheKey] = $page;
        }

        return $page;
    }

    /**
     * Load our OneHydra page entity for the given request
     *
     * @param Request $request
     * @return OneHydraPageInterface|null
     */
    public function getPageByRequest(Request $request)
    {
        if ($pageName = $request->attributes->get(self::REQUEST_KEY_PAGE_NAME)) {
            $programId = $request->attributes->get(self::REQUEST_KEY_PROGRAM);

            return $this->getPage($pageName, $programId);
        }

        $programId = $this->pageTransformStrategy->getLookupProgramId($request);
        $pageName = $this->pageTransformStrategy->getLookupPageName($request);

        $pageEntity = $this->getPage($pageName, $programId);

        if ($pageEntity) {
            $request->attributes->set(self::REQUEST_KEY_PAGE_NAME, $pageName);
            $request->attributes->set(self::REQUEST_KEY_PROGRAM, $programId);
        }

        return $pageEntity;
    }
}