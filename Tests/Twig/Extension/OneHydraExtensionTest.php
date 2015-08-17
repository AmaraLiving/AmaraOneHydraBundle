<?php
/**
 * Created by PhpStorm.
 * User: vincenzotrapani
 * Date: 17/08/15
 * Time: 11:46
 */

namespace Amara\Bundle\OneHydraBundle\Tests\Twig\Extension;


use Amara\Bundle\OneHydraBundle\State\CurrentPageState;
use Amara\Bundle\OneHydraBundle\Twig\Extension\OneHydraExtension;
use Amara\OneHydra\Object\PageObject;

class OneHydraExtensionTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @throws \Twig_Error_Runtime
	 * @cover OneHydraExtension::getOneHydraHeadContent
	 */
	public function testOneHydraHeadContent() {

		$extension = new OneHydraExtension();

		$twig = new \Twig_Environment(new \Twig_Loader_String(), [
			'debug' => false,
			'cache' => false,
			'optimizations' => 0,
		]);


		$currentPage = new CurrentPageState();

		$extension->setCurrentPageState($currentPage);

		$twig->addExtension($extension);


		$ext = $twig->getExtension('onehydra_extension');

		// With no page found must return the default value
		$this->assertEquals($ext->getOneHydraHeadContent('description', 'defaultDescription'), 'defaultDescription');
		$this->assertEquals($ext->getOneHydraHeadContent('keywords', 'defaultKeywords'), 'defaultKeywords');
		$this->assertEquals($ext->getOneHydraHeadContent('title', 'defaultTitle'), 'defaultTitle');


		$currentPage->setPage($this->getPageObject(true));

		$this->assertTrue(array_key_exists('oneHydraHeadContent', $twig->getFunctions()));

		$ext = $twig->getExtension('onehydra_extension');

		// With null value must return the default value
		$this->assertEquals($ext->getOneHydraHeadContent('description', 'defaultDescription'), 'defaultDescription');
		$this->assertEquals($ext->getOneHydraHeadContent('keywords', 'defaultKeywords'), 'defaultKeywords');
		$this->assertEquals($ext->getOneHydraHeadContent('title', 'defaultTitle'), 'defaultTitle');


		$currentPage->setPage($this->getPageObject());
		$extension->setCurrentPageState($currentPage);

		// With not null value must return the onehydra value
		$this->assertEquals($ext->getOneHydraHeadContent('description', ''), 'ThisIsTheMetaDescription');
		$this->assertEquals($ext->getOneHydraHeadContent('keywords', ''), 'ThisIsTheMetaKeywords');
		$this->assertEquals($ext->getOneHydraHeadContent('title', ''), 'ThisIsTheTitle');

	}

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


	/**
	 * @cover OneHydraExtension::getFunctions
	 * @cover OneHydraExtension::getName
	 */
	public function testExtensionSettings() {
		$oneHydraExtension = new OneHydraExtension();
		$functions = $oneHydraExtension->getFunctions();

		$this->assertTrue(array_key_exists('oneHydraHeadContent', $functions));

		$this->assertInstanceOf('\Twig_Function_Method', $functions['oneHydraHeadContent']);

		$this->assertEquals('onehydra_extension', $oneHydraExtension->getName());
	}
}