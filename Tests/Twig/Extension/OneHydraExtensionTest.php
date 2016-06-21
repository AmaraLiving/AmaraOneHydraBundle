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
        $ext = $twig->getExtension('onehydra_extension');

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

    public function testOneHydraHeadContent()
    {
        $ext = new OneHydraExtension();

        $currentPage = new OneHydraPage();
        $currentPage->setPageObject($this->getPageObject(true));

        $request = $this->prophesize(Request::class);

        $pageManager = $this->prophesize(PageManager::class);
        $pageManager->getPageByRequest($request)->willReturn($currentPage);

        $ext->setPageManager($pageManager->reveal());

        $this->assertInstanceOf(OneHydraExtension::class, $ext);

        // With no page found must return the default value
        $this->assertEquals(
            $ext->getOneHydraHeadContent('description', 'defaultDescription', $request->reveal()),
            'defaultDescription'
        );
        $this->assertEquals(
            $ext->getOneHydraHeadContent('keywords', 'defaultKeywords', $request->reveal()),
            'defaultKeywords'
        );
        $this->assertEquals($ext->getOneHydraHeadContent('title', 'defaultTitle', $request->reveal()), 'defaultTitle');

        // With null value must return the default value
        $this->assertEquals(
            $ext->getOneHydraHeadContent('description', 'defaultDescription', $request->reveal()),
            'defaultDescription'
        );
        $this->assertEquals(
            $ext->getOneHydraHeadContent('keywords', 'defaultKeywords', $request->reveal()),
            'defaultKeywords'
        );
        $this->assertEquals($ext->getOneHydraHeadContent('title', 'defaultTitle', $request->reveal()), 'defaultTitle');
        /*
                $currentPage->setPage($this->getPageObject());
                $extension->setCurrentPageState($currentPage);

                // With not null value must return the onehydra value
                $this->assertEquals($ext->getOneHydraHeadContent('description', '', $request->reveal()), 'ThisIsTheMetaDescription');
                $this->assertEquals($ext->getOneHydraHeadContent('keywords', '', $request->reveal()), 'ThisIsTheMetaKeywords');
                $this->assertEquals($ext->getOneHydraHeadContent('title', '', $request->reveal()), 'ThisIsTheTitle');
        */
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