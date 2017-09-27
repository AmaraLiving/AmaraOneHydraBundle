<?php

namespace Amara\Bundle\OneHydraBundle\Tests\Entity;

use Amara\Bundle\ImageBundle\Tests\BaseTestCase;
use Amara\Bundle\OneHydraBundle\Entity\OneHydraPage;
use Amara\Bundle\OneHydraBundle\Storage\PageStorageInterface;

class OneHydraPageTest extends BaseTestCase
{
    /**
     * @var OneHydraPage
     */
    private $oneHydraPage;

    public function setUp()
    {
        $this->oneHydraPage = new OneHydraPage();
    }

    /**
     * @covers Amara\Bundle\OneHydraBundle\Entity\OneHydraPage::getId
     * @covers Amara\Bundle\OneHydraBundle\Entity\OneHydraPage::setId
     */
    public function testIdGetterAndSetter()
    {
        $this->assertNull($this->oneHydraPage->getId());
        $this->oneHydraPage->setId(123);
        $this->assertEquals(123, $this->oneHydraPage->getId());
    }

    /**
     * @covers Amara\Bundle\OneHydraBundle\Entity\OneHydraPage::getPageName
     * @covers Amara\Bundle\OneHydraBundle\Entity\OneHydraPage::setPageName
     */
    public function testPageNameGetterAndSetter()
    {
        $this->assertNull($this->oneHydraPage->getPageName());
        $this->oneHydraPage->setPageName('Test Page Name');
        $this->assertEquals('Test Page Name', $this->oneHydraPage->getPageName());
    }

    /**
     * @covers Amara\Bundle\OneHydraBundle\Entity\OneHydraPage::getPageObject
     * @covers Amara\Bundle\OneHydraBundle\Entity\OneHydraPage::setPageObject
     */
    public function testPageObjectGetterAndSetter()
    {
        $pageObject = $this->prophesize(PageStorageInterface::class);

        $this->assertNull($this->oneHydraPage->getPageObject());
        $this->oneHydraPage->setPageObject($pageObject->reveal());
        $this->assertEquals($pageObject->reveal(), $this->oneHydraPage->getPageObject());
    }

    /**
     * @covers Amara\Bundle\OneHydraBundle\Entity\OneHydraPage::getProgramId
     * @covers Amara\Bundle\OneHydraBundle\Entity\OneHydraPage::setProgramId
     */
    public function testProgramIdGetterAndSetter()
    {
        $this->assertNull($this->oneHydraPage->getProgramId());
        $this->oneHydraPage->setProgramId('Test Program Id');
        $this->assertEquals('Test Program Id', $this->oneHydraPage->getProgramId());
    }

    /**
     * @covers Amara\Bundle\OneHydraBundle\Entity\OneHydraPage::getCreatedAt
     * @covers Amara\Bundle\OneHydraBundle\Entity\OneHydraPage::setCreatedAt
     */
    public function testCreatedAtGetterAndSetter()
    {
        $date = new \DateTime('2000-01-01');

        $this->assertNull($this->oneHydraPage->getCreatedAt());
        $this->oneHydraPage->setCreatedAt($date);
        $this->assertEquals($date, $this->oneHydraPage->getCreatedAt());
    }
}