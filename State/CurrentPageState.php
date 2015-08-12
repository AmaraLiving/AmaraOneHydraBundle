<?php
namespace Amara\Bundle\OneHydraBundle\State;

use Amara\OneHydra\Object\PageObject;

class CurrentPageState {

	/** @var PageObject */
	private $page = null;

	/**
	 * @return PageObject
	 */
	public function getPage() {
		return $this->page;
	}

	/**
	 * @param PageObject $page
	 */
	public function setPage(PageObject $page) {
		$this->page = $page;
	}
}