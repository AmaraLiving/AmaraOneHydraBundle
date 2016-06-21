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

use Amara\Bundle\OneHydraBundle\Strategy\DefaultPageTransformStrategy;
use Amara\OneHydra\Model\PageInterface;
use PHPUnit_Framework_TestCase;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;

class DefaultPageTransformStrategyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var DefaultPageTransformStrategy
     */
    private $defaultPageTransformStrategy;

    public function setUp()
    {
        $this->defaultPageTransformStrategy = new DefaultPageTransformStrategy();
    }

    public function testGetLookupPageName()
    {
        $pathInfo = '/my/url';

        $request = $this->prophesize(Request::class);
        $request->getPathInfo()->willReturn($pathInfo);

        $actual = $this->defaultPageTransformStrategy->getLookupPageName($request->reveal());

        $this->assertEquals($pathInfo, $actual);
    }

    public function testGetLookupProgramId()
    {
        $request = $this->prophesize(Request::class);

        $actual = $this->defaultPageTransformStrategy->getLookupProgramId($request->reveal());

        $this->assertEquals('', $actual);
    }

    public function testGetPageNameForStorage()
    {
        $url = 'https://www.example.com/foo';

        $page = $this->prophesize(PageInterface::class);
        $page->getPageUrl()->willReturn($url);

        $actual = $this->defaultPageTransformStrategy->getPageNameForStorage($page->reveal());

        $this->assertEquals($url, $actual);
    }

    public function testTransformDisplayUrl()
    {
        $url = 'https://www.example.com/foo';

        $actual = $this->defaultPageTransformStrategy->transformDisplayUrl($url);

        $this->assertEquals($url, $actual);
    }

    public function testTransformPageForStorage()
    {
        $page = $this->prophesize(PageInterface::class);

        $actual = $this->defaultPageTransformStrategy->transformPageForStorage($page->reveal());

        $this->assertEquals($page->reveal(), $actual);
    }
}