<?php

/*
 * This file is part of the AmaraOneHydraBundle package.
 *
 * (c) Amara Living Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Amara\Bundle\OneHydraBundle\Tests\EventListener;

use Amara\Bundle\OneHydraBundle\Entity\OneHydraPageInterface;
use Amara\Bundle\OneHydraBundle\Service\PageManager;
use Amara\Bundle\OneHydraBundle\Storage\PageStorageInterface;
use Amara\Bundle\OneHydraBundle\Strategy\PageTransformStrategyInterface;
use Amara\OneHydra\Model\PageInterface;
use PHPUnit_Framework_TestCase;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;

class PageManagerTest extends PHPUnit_Framework_TestCase
{
    public function testAddPage()
    {
        $page = $this->prophesize(PageInterface::class);
        $programId = 'foo';

        $pageName = '/foo';

        $pageTransformStrategy = $this->prophesize(PageTransformStrategyInterface::class);
        $pageTransformStrategy->transformPageForStorage($page)->willReturn($page);
        $pageTransformStrategy->getPageNameForStorage($page)->willReturn($pageName);

        $pageStorage = $this->prophesize(PageStorageInterface::class);
        $pageStorage->addPage($page, $pageName, $programId)->shouldBeCalled();

        $pageManager = new PageManager();
        $pageManager->setPageStorage($pageStorage->reveal());
        $pageManager->setPageTransformStrategy($pageTransformStrategy->reveal());

        $pageManager->addPage($page->reveal(), $programId);
    }

    public function testGetPage()
    {
        $pageEntity = $this->prophesize(OneHydraPageInterface::class);
        $programId = 'foo';

        $pageName = '/foo';

        $pageStorage = $this->prophesize(PageStorageInterface::class);

        // Should be called just once because of the local cache!
        $pageStorage->getPageEntity($pageName, $programId)->willReturn($pageEntity)->shouldBeCalledTimes(2);

        $pageManager = new PageManager();
        $pageManager->setPageStorage($pageStorage->reveal());

        $actualPageEntity1 = $pageManager->getPage($pageName, $programId);
        $actualPageEntity2 = $pageManager->getPage($pageName, $programId);

        $this->assertEquals($pageEntity->reveal(), $actualPageEntity1);
        $this->assertEquals($pageEntity->reveal(), $actualPageEntity2);
    }

    public function testGetPageByRequestWithoutAttributesAlready()
    {
        $request = Request::create('/foo/bar');

        $pageEntity = $this->prophesize(OneHydraPageInterface::class);

        $programId = 'foo';
        $pageName = '/foo';

        $pageStorage = $this->prophesize(PageStorageInterface::class);
        // Should be called just once because of the local cache!
        $pageStorage->getPageEntity($pageName, $programId)->willReturn($pageEntity)->shouldBeCalledTimes(2);

        $pageTransformStrategy = $this->prophesize(PageTransformStrategyInterface::class);
        $pageTransformStrategy->getLookupProgramId($request)->willReturn($programId);
        $pageTransformStrategy->getLookupPageName($request)->willReturn($pageName);

        $pageManager = new PageManager();
        $pageManager->setPageStorage($pageStorage->reveal());
        $pageManager->setPageTransformStrategy($pageTransformStrategy->reveal());

        $actualPageEntity1 = $pageManager->getPageByRequest($request);
        $actualPageEntity2 = $pageManager->getPageByRequest($request);

        $this->assertEquals($pageEntity->reveal(), $actualPageEntity1);
        $this->assertEquals($pageEntity->reveal(), $actualPageEntity2);

        $this->assertEquals($pageName, $request->attributes->get('_one_hydra_name'));
        $this->assertEquals($programId, $request->attributes->get('_one_hydra_program'));
    }

    public function testGetPageByRequestWithAttributesAlready()
    {
        $programId = 'foo';
        $pageName = '/foo';

        $request = Request::create('/foo/bar');
        $request->attributes->set('_one_hydra_name', $pageName);
        $request->attributes->set('_one_hydra_program', $programId);

        $pageEntity = $this->prophesize(OneHydraPageInterface::class);

        $pageStorage = $this->prophesize(PageStorageInterface::class);
        // Should be called just once because of the local cache!
        $pageStorage->getPageEntity($pageName, $programId)->willReturn($pageEntity)->shouldBeCalledTimes(2);

        $pageTransformStrategy = $this->prophesize(PageTransformStrategyInterface::class);
        $pageTransformStrategy->getLookupProgramId($request)->shouldNotBeCalled();
        $pageTransformStrategy->getLookupPageName($request)->shouldNotBeCalled();

        $pageManager = new PageManager();
        $pageManager->setPageStorage($pageStorage->reveal());
        $pageManager->setPageTransformStrategy($pageTransformStrategy->reveal());

        $actualPageEntity1 = $pageManager->getPageByRequest($request);
        $actualPageEntity2 = $pageManager->getPageByRequest($request);

        $this->assertEquals($pageEntity->reveal(), $actualPageEntity1);
        $this->assertEquals($pageEntity->reveal(), $actualPageEntity2);

        $this->assertEquals($pageName, $request->attributes->get('_one_hydra_name'));
        $this->assertEquals($programId, $request->attributes->get('_one_hydra_program'));
    }
}