<?php

namespace Amara\Bundle\OneHydraBundle\Tests\Twig\Extension;


use Amara\Bundle\OneHydraBundle\Entity\OneHydraPage;
use Amara\Bundle\OneHydraBundle\Service\PageManager;
use Amara\Bundle\OneHydraBundle\State\CurrentPageState;
use Amara\Bundle\OneHydraBundle\Twig\Extension\OneHydraExtension;
use Amara\OneHydra\Object\PageObject;
use Symfony\Component\HttpFoundation\Request;
use Twig_Environment;
use Twig_Loader_String;
use Twig_SimpleFunction;

class OneHydraExtensionTest extends \PHPUnit_Framework_TestCase {
	public function testCheckThatTwigFunctionsAreRegistered() {
		$extension = new OneHydraExtension();

		$twig = new Twig_Environment(new Twig_Loader_String(), [
			'debug' => false,
			'cache' => false,
			'optimizations' => 0,
		]);

		$twig->addExtension($extension);

		/** @var OneHydraExtension $ext */
		$ext = $twig->getExtension('onehydra_extension');

		$this->assertInstanceOf(OneHydraExtension::class, $ext);

		$function = $twig->getFunction('oneHydraHeadContent');
		$this->assertInstanceOf(Twig_SimpleFunction::class, $function);

		$function = $twig->getFunction('oneHydraIsSuggestedPage');
		$this->assertInstanceOf(Twig_SimpleFunction::class, $function);
	}

	public function testOneHydraHeadContent() {
		$ext = new OneHydraExtension();

		$currentPage = new OneHydraPage();
		$currentPage->setPageObject($this->getPageObject(true));

		$request = $this->prophesize(Request::class);

		$pageManager = $this->prophesize(PageManager::class);
		$pageManager->getPageByRequest($request)->willReturn($currentPage);

		$ext->setPageManager($pageManager->reveal());

		$this->assertInstanceOf(OneHydraExtension::class, $ext);

		// With no page found must return the default value
		$this->assertEquals($ext->getOneHydraHeadContent('description', 'defaultDescription', $request->reveal()), 'defaultDescription');
		$this->assertEquals($ext->getOneHydraHeadContent('keywords', 'defaultKeywords', $request->reveal()), 'defaultKeywords');
		$this->assertEquals($ext->getOneHydraHeadContent('title', 'defaultTitle', $request->reveal()), 'defaultTitle');

		// With null value must return the default value
		$this->assertEquals($ext->getOneHydraHeadContent('description', 'defaultDescription', $request->reveal()), 'defaultDescription');
		$this->assertEquals($ext->getOneHydraHeadContent('keywords', 'defaultKeywords', $request->reveal()), 'defaultKeywords');
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
	 * @return PageObject
	 */
	public function getPageObject($mustBeNull = false) {
		$pageObject = new PageObject();
		$pageObject->setPageName('/testpage');

		$headObject = new \stdClass;
		$headObject->MetaDescription = ($mustBeNull) ? null : 'ThisIsTheMetaDescription';
		$headObject->MetaKeywords =  ($mustBeNull) ? null : 'ThisIsTheMetaKeywords';
		$headObject->Title =  ($mustBeNull) ? null : 'ThisIsTheTitle';

		$pageObject->setHeadContent($headObject);

		return $pageObject;
	}

	public function testExtensionSettings() {
		$oneHydraExtension = new OneHydraExtension();
		$functions = $oneHydraExtension->getFunctions();

		$this->assertTrue(array_key_exists('oneHydraHeadContent', $functions));

		$this->assertInstanceOf('\Twig_SimpleFunction', $functions['oneHydraHeadContent']);

		$this->assertEquals('onehydra_extension', $oneHydraExtension->getName());
	}
}