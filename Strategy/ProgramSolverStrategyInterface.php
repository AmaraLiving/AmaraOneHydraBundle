<?php

namespace Amara\Bundle\OneHydraBundle\Strategy;

interface ProgramSolverStrategyInterface {

 	/**
 	 * A custom logic to get the right program id must be 
 	 * implemented in the getProgramId method
 	 */ 
	public function getProgramId();
}