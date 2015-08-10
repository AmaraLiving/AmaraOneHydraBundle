<?php

namespace Amara\Bundle\OneHydraBundle\Strategy;

class EmptyProgramSolverStrategy implements ProgramSolverStrategyInterface {

	public function getProgramId() {
		throw new Exception('You must implement a custom ProgramSolverStrategy');
	}
}