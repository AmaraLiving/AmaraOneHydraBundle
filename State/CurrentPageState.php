<?php
namespace Amara\Bundle\OneHydraBundle\State;

use Amara\OneHydra\Object\PageObjectInterface;

class CurrentPageState {

	/** @var PageObjectInterface */
	private $page = null;

	/**
	 * @return PageObjectInterface
	 */
	public function getPage() {
		return $this->page;
	}

	/**
	 * @param PageObjectInterface $page
	 */
	public function setPage(PageObjectInterface $page) {
		$this->page = $page;
	}
}