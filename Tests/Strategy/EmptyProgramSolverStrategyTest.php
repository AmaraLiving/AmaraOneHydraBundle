<?php
/**
 * Created by PhpStorm.
 * User: vincenzotrapani
 * Date: 17/08/15
 * Time: 11:32
 */
namespace Amara\Bundle\OneHydraBundle\Tests\Strategy;

use Amara\Bundle\OneHydraBundle\Strategy\EmptyProgramSolverStrategy;

class EmptyProgramSolverStrategyTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @cover EmptyProgramSolverStrategy::getProgramId
	 * @expectedException \Exception
	 */
	public function testThatAnExceptionIsRaisedIfThisClassIsUsed() {
		$programSolver = new EmptyProgramSolverStrategy();

		$programSolver->getProgramId();
	}
}