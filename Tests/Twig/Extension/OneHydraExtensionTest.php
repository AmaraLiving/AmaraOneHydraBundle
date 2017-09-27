<?php

/*
 * This file is part of the AmaraOneHydraBundle package.
 *
 * (c) Amara Living Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Amara\Bundle\OneHydraBundle\Tests\Twig\Extension;

use Amara\Bundle\OneHydraBundle\Entity\OneHydraPage;
use Amara\Bundle\OneHydraBundle\Entity\OneHydraPageInterface;
use Amara\Bundle\OneHydraBundle\Service\PageManager;
use Amara\Bundle\OneHydraBundle\Twig\Extension\OneHydraExtension;
use Amara\OneHydra\Model\Page;
use Amara\OneHydra\Model\PageInterface;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig_Environment;
use Twig_Loader_Array;
use Twig_SimpleFunction;

class OneHydraExtensionTest extends PHPUnit_Framework_TestCase
{
    public function testCheckThatTwigFunctionsAreRegistered()
    {
        $extension = new OneHydraExtension();

        $twig = new Twig_Environment(
            new Twig_Loader_Array([]), [
                'debug' => false,
                'cache' => false,
                'optimizations' => 0,
            ]
        );

        $twig->addExtension($extension);

        /** @var OneHydraExtension $ext */
        $ext = $twig->getExtension(OneHydraExtension::class);

        $this->assertInstanceOf(OneHydraExtension::class, $ext);

        $function = $twig->getFunction('oneHydraHeadContent');
        $this->assertInstanceOf(Twig_SimpleFunction::class, $function);

        $function = $twig->getFunction('oneHydraIsSuggestedPage');
        $this->assertInstanceOf(Twig_SimpleFunction::class, $function);
    }

    public function testIsSuggestedWithGivenRequestAndExistingEntity()
    {
        $isSuggested = 'foo';

        $currentPage = new OneHydraPage();
        $currentPage->setPageObject($this->getPageObject(true));

        $page = $this->prophesize(PageInterface::class);
        $page->isSuggested()->willReturn($isSuggested);

        $currentPage = new OneHydraPage();
        $currentPage->setPageObject($page->reveal());

        $request = $this->prophesize(Request::class);

        $pageManager = $this->prophesize(PageManager::class);
        $pageManager->getPageByRequest($request)->willReturn($currentPage);

        $extension = new OneHydraExtension();
        $extension->setPageManager($pageManager->reveal());

        $actual = $extension->isSuggested($request->reveal());

        $this->assertEquals($actual, $isSuggested);
    }

    public function testIsSuggestedWithGivenRequestAndNoExistingEntity()
    {
        $request = $this->prophesize(Request::class);

        $pageManager = $this->prophesize(PageManager::class);
        $pageManager->getPageByRequest($request)->willReturn(null);

        $extension = new OneHydraExtension();
        $extension->setPageManager($pageManager->reveal());

        $actual = $extension->isSuggested($request->reveal());

        $this->assertFalse($actual);
    }

    public function testIsSuggestedWithDefaultRequestAndNoExistingEntity()
    {
        $request = $this->prophesize(Request::class);
        $requestStack = $this->prophesize(RequestStack::class);
        $requestStack->getCurrentRequest()->willReturn($request);

        $pageManager = $this->prophesize(PageManager::class);
        $pageManager->getPageByRequest($request)->willReturn(null);

        $extension = new OneHydraExtension();
        $extension->setPageManager($pageManager->reveal());

        $actual = $extension->isSuggested($request->reveal());

        $this->assertFalse($actual);
    }

    public function testIsSuggestedWithoutRequest()
    {
        $requestStack = $this->prophesize(RequestStack::class);
        $requestStack->getCurrentRequest()->willReturn(null);

        $extension = new OneHydraExtension();
        $extension->setRequestStack($requestStack->reveal());

        $this->assertFalse($extension->isSuggested());
    }

    public function testOneHydraHeadContentWithNullRequest()
    {
        $request = $this->prophesize(RequestStack::class);
        $request->getCurrentRequest()->willReturn(null);

        $ext = new OneHydraExtension();
        $ext->setRequestStack($request->reveal());

        $this->assertFalse(
            $ext->getOneHydraHeadContent('description', 'defaultDescription')
        );
    }

    public function testOneHydraHeadContentWithoutPage()
    {
        $request = $this->prophesize(Request::class);

        $pageManager = $this->prophesize(PageManager::class);
        $pageManager->getPageByRequest($request)->willReturn(null);

        $requestStack = $this->prophesize(RequestStack::class);
        $requestStack->getCurrentRequest()->willReturn($request->reveal());

        $ext = new OneHydraExtension();
        $ext->setRequestStack($requestStack->reveal());
        $ext->setPageManager($pageManager->reveal());

        $this->assertEquals(
            'defaultDescription',
            $ext->getOneHydraHeadContent('description', 'defaultDescription')
        );
    }

    public function testOneHydraHeadContentWithPageButWithoutPageObject()
    {
        $request = $this->prophesize(Request::class);

        $page = $this->prophesize(OneHydraPageInterface::class);
        $page->getPageObject()->willReturn(null);

        $pageManager = $this->prophesize(PageManager::class);
        $pageManager->getPageByRequest($request)->willReturn($page->reveal());

        $ext = new OneHydraExtension();
        $ext->setPageManager($pageManager->reveal());

        $this->assertEquals(
            'defaultDescription',
            $ext->getOneHydraHeadContent('description', 'defaultDescription', $request->reveal())
        );
    }

    public function testOneHydraHeadContentWithPageWithPageObjectButWithoutExistingMethod()
    {
        $request = $this->prophesize(Request::class);

        $pageObject = $this->prophesize(PageInterface::class);

        $page = $this->prophesize(OneHydraPageInterface::class);
        $page->getPageObject()->willReturn($pageObject->reveal());

        $pageManager = $this->prophesize(PageManager::class);
        $pageManager->getPageByRequest($request)->willReturn($page->reveal());

        $ext = new OneHydraExtension();
        $ext->setPageManager($pageManager->reveal());

        $this->assertEquals(
            'defaultDescription',
            $ext->getOneHydraHeadContent('description', 'defaultDescription', $request->reveal())
        );
    }

    public function testOneHydraHeadContentWithPageWithPageObjectWithExitingMethodButWhichReturnsNull()
    {
        $request = $this->prophesize(Request::class);

        $pageObject = $this->prophesize(PageInterface::class);
        $pageObject->getDescription()->willReturn(null);

        $page = $this->prophesize(OneHydraPageInterface::class);
        $page->getPageObject()->willReturn($pageObject->reveal());

        $pageManager = $this->prophesize(PageManager::class);
        $pageManager->getPageByRequest($request)->willReturn($page->reveal());

        $ext = new OneHydraExtension();
        $ext->setPageManager($pageManager->reveal());

        $this->assertEquals(
            'defaultDescription',
            $ext->getOneHydraHeadContent('description', 'defaultDescription', $request->reveal())
        );
    }

    public function testOneHydraHeadContentWithPageWithPageObjectWithExitingMethod()
    {
        $request = $this->prophesize(Request::class);

        $pageObject = $this->prophesize(PageInterface::class);
        $pageObject->getDescription()->willReturn('description value');

        $page = $this->prophesize(OneHydraPageInterface::class);
        $page->getPageObject()->willReturn($pageObject->reveal());

        $pageManager = $this->prophesize(PageManager::class);
        $pageManager->getPageByRequest($request)->willReturn($page->reveal());

        $ext = new OneHydraExtension();
        $ext->setPageManager($pageManager->reveal());

        $this->assertEquals(
            'description value',
            $ext->getOneHydraHeadContent('description', 'defaultDescription', $request->reveal())
        );
    }

    /**
     * @param bool $mustBeNull
     * @return Page
     */
    private function getPageObject($mustBeNull = false)
    {
        $rawPageHead = new \stdClass;
        $rawPageHead->MetaDescription = ($mustBeNull) ? null : 'ThisIsTheMetaDescription';
        $rawPageHead->MetaKeywords = ($mustBeNull) ? null : 'ThisIsTheMetaKeywords';
        $rawPageHead->Title = ($mustBeNull) ? null : 'ThisIsTheTitle';

        $rawPage = new \stdClass;
        $rawPage->HeadContent = $rawPageHead;

        $page = new Page($rawPage);
        $page->setPageUrl('/testpage');

        return $page;
    }

    public function testExtensionSettings()
    {
        $oneHydraExtension = new OneHydraExtension();
        $functions = $oneHydraExtension->getFunctions();

        $this->assertTrue(array_key_exists('oneHydraHeadContent', $functions));

        $this->assertInstanceOf('\Twig_SimpleFunction', $functions['oneHydraHeadContent']);

        $this->assertEquals('onehydra_extension', $oneHydraExtension->getName());
    }
}