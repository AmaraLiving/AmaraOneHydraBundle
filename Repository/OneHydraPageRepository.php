<?php

namespace Amara\Bundle\OneHydraBundle\Repository;

use Doctrine\ORM\EntityRepository;

class OneHydraPageRepository extends EntityRepository {

	/**
	 * @param string $programId
	 * @return array
	 */
	public function findByProgramId($programId = 'uk') {
		return $this->findBy(['programId' => $programId]);
	}

	/**
	 * @param string $pageName
	 * @param string $programId
	 * @return object
	 */
	public function findOneByPageName($pageName, $programId) {
		return $this->findOneBy(['pageName' => $pageName, 'programId' => $programId]);
	}
}