<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2009 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once dirname ( __FILE__ ) . '/../../AbstractTestcase.php';

/**
 * test case for Tx_Extracache_Domain_Repository_CleanerInstructionRepository
 * @package extracache_tests
 * @subpackage Domain_Repository
 */
class Tx_Extracache_Domain_Repository_CleanerInstructionRepositoryTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * 
	 * @var Tx_Extracache_Domain_Repository_CleanerInstructionRepository
	 */
	private $repository;
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->loadClass('Tx_Extracache_Domain_Model_CleanerInstruction');
		$this->loadClass('Tx_Extracache_Domain_Repository_CleanerInstructionRepository');
		$this->repository = new Tx_Extracache_Domain_Repository_CleanerInstructionRepository();
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->repository );
	}

	/**
	 * test method addCleanerInstruction
	 * @test
	 */
	public function addCleanerInstruction() {
		$cleanerInstruction1 = $this->getMock ( 'Tx_Extracache_Domain_Model_CleanerInstruction', array (), array (), '', FALSE );
		$cleanerInstruction2 = $this->getMock ( 'Tx_Extracache_Domain_Model_CleanerInstruction', array (), array (), '', FALSE );
		
		$this->assertTrue ( count($this->repository->getCleanerInstructions ()) === 0 );
		$this->repository->addCleanerInstruction($cleanerInstruction1);
		$this->assertTrue ( count($this->repository->getCleanerInstructions ()) === 1 );
		$this->repository->addCleanerInstruction($cleanerInstruction2);
		$this->assertTrue ( count($this->repository->getCleanerInstructions ()) === 2 );
	}
}