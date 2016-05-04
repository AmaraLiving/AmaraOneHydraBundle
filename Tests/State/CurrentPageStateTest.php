<?php

namespace Amara\Bundle\OneHydraBundle\Tests\State;

use Amara\Bundle\OneHydraBundle\State\CurrentPageState;
use Amara\OneHydra\Object\PageObject;

class CurrentPageStateTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @cover CurrentPageState::setPage
	 * @cover CurrentPageState::getPage
	 */
	public function testPageObjectReturnedIsCorrect() {
		$pageObject = new PageObject();

		$currentPageState = new CurrentPageState();
		$currentPageState->setPage($pageObject);

		$this->assertEquals($pageObject, $currentPageState->getPage());
	}
}