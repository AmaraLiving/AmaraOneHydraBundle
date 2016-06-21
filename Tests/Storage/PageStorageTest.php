<?php

/*
 * This file is part of the AmaraOneHydraBundle package.
 *
 * (c) Amara Living Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Amara\Bundle\OneHydraBundle\Tests\Strategy;

use Amara\Bundle\OneHydraBundle\Entity\OneHydraPage;
use Amara\Bundle\OneHydraBundle\Entity\OneHydraPageInterface;
use Amara\Bundle\OneHydraBundle\Storage\PageStorage;
use Amara\OneHydra\Model\PageInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit_Framework_TestCase;
use Prophecy\Argument;
use PHPUnit_Framework_Assert;

class PageStorageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PageStorage
     */
    private $pageStorage;

    private $entityManager;

    private $repository;

    public function setUp()
    {
        $this->pageStorage = new PageStorage();

        $this->entityManager = $this->prophesize(EntityManager::class);
        $this->repository = $this->prophesize(EntityRepository::class);
        $this->entityManager->getRepository('AmaraOneHydraBundle:OneHydraPage')->willReturn($this->repository);

        $this->pageStorage->setEntityManager($this->entityManager->reveal());
    }

    public function testAddPage()
    {
        $page = $this->prophesize(PageInterface::class);
        $pageName = '/my/url';
        $programId = 'example';

        $existingPageEntity = $this->prophesize(OneHydraPageInterface::class);

        // We'll have an entity there already
        $this->repository->findOneBy(
            [
                'pageName' => $pageName,
                'programId' => $programId,
            ]
        )->willReturn($existingPageEntity);

        // We expect it to be removed
        $this->entityManager->remove($existingPageEntity)->shouldBeCalled();

        $this->entityManager->persist(Argument::that(function ($pageEntity) use ($pageName, $programId, $page) {
            PHPUnit_Framework_Assert::assertEquals($pageName, $pageEntity->getPageName());
            PHPUnit_Framework_Assert::assertEquals($programId, $pageEntity->getProgramId());
            PHPUnit_Framework_Assert::assertEquals($page->reveal(), $pageEntity->getPageObject());

            // todo: assert date!

            return true;
        }))->shouldBeCalled();
        $this->entityManager->flush()->shouldBeCalled();

        $this->pageStorage->addPage($page->reveal(), $pageName, $programId);
    }

    public function testGetPageEntity()
    {
        $pageName = '/my/url';
        $programId = 'example';

        $pageEntity = $this->prophesize(OneHydraPageInterface::class);

        // We'll have an entity there already
        $this->repository->findOneBy(
            [
                'pageName' => $pageName,
                'programId' => $programId,
            ]
        )->willReturn($pageEntity);

        $actual = $this->pageStorage->getPageEntity($pageName, $programId);

        $this->assertEquals($pageEntity->reveal(), $actual);
    }
}