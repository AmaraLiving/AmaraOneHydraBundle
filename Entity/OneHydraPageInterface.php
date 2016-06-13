<?php

/*
 * This file is part of the AmaraOneHydraBundle package.
 *
 * (c) Amara Living Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Amara\Bundle\OneHydraBundle\Entity;

use Amara\OneHydra\Model\PageInterface;

/**
 * OneHydraPageInterface
 */
interface OneHydraPageInterface
{
    /**
     * @return string
     */
    public function getPageName();

    /**
     * @return PageInterface
     */
    public function getPageObject();

    /**
     * @return string
     */
    public function getProgramId();
}