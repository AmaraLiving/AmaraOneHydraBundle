<?php

/*
 * This file is part of the AmaraOneHydraBundle package.
 *
 * (c) Amara Living Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Amara\Bundle\OneHydraBundle\Storage;

use Amara\Bundle\OneHydraBundle\Entity\OneHydraPageInterface;
use Amara\OneHydra\Model\PageInterface;

/**
 * PageStorageInterface
 *
 * This is responsible for loading and saving our page entity
 */
interface PageStorageInterface
{
    /**
     * @param PageInterface $page
     * @param string $pageName
     * @param string $programId
     */
    public function addPage(PageInterface $page, $pageName, $programId);

    /**
     * @param string $pageName
     * @param string $programId
     * @return OneHydraPageInterface
     */
    public function getPageEntity($pageName, $programId);
}