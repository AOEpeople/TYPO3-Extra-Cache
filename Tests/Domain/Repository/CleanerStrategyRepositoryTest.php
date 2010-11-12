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
 * test case for Tx_Extracache_Domain_Repository_CleanerStrategyRepository
 * @package extracache
 * @subpackage Tests_Domain_Repository
 */
class Tx_Extracache_Tests_Domain_Repository_CleanerStrategyRepositoryTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * 
	 * @var Tx_Extracache_Domain_Repository_CleanerStrategyRepository
	 */
	private $repository;
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->loadClass('Tx_Extracache_Domain_Model_CleanerStrategy');
		$this->loadClass('Tx_Extracache_Domain_Repository_CleanerStrategyRepository');
		$this->repository = new Tx_Extracache_Domain_Repository_CleanerStrategyRepository();
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->repository );
	}

	/**
	 * test method addStrategy
	 * @test
	 */
	public function addStrategy() {
		$strategy1 = t3lib_div::makeInstance('Tx_Extracache_Domain_Model_CleanerStrategy', 1, Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenNoAction, Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsOnly, 'key1', 'name1');
		$strategy2 = t3lib_div::makeInstance('Tx_Extracache_Domain_Model_CleanerStrategy', 1, Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenNoAction, Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsOnly, 'key1', 'name1');
		$this->assertTrue ( count($this->repository->getCleanerStrategies ()) === 0 );
		$this->repository->addStrategy($strategy1);
		$this->assertTrue ( count($this->repository->getCleanerStrategies ()) === 1 );
		$this->repository->addStrategy($strategy2);
		$this->assertTrue ( count($this->repository->getCleanerStrategies ()) === 2 );
	}
	/**
	 * test method hasStrategy
	 * @test
	 */
	public function hasStrategy() {
		$strategy1 = t3lib_div::makeInstance('Tx_Extracache_Domain_Model_CleanerStrategy', 1, Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenNoAction, Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsOnly, 'key1', 'name1');
		$this->assertFalse ( $this->repository->hasStrategy ('key1') );
		$this->repository->addStrategy($strategy1);
		$this->assertTrue ( $this->repository->hasStrategy ('key1') );
	}
	/**
	 * test method getStrategy
	 * @test
	 */
	public function canGetStrategy() {
		$strategy1 = t3lib_div::makeInstance('Tx_Extracache_Domain_Model_CleanerStrategy', 1, Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenNoAction, Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsOnly, 'key1', 'name1');
		$this->repository->addStrategy($strategy1);
		$this->assertEquals ( $this->repository->getStrategy ('key1'), $strategy1 );
	}
	/**
	 * test method getStrategy
	 * @test
	 * @expectedException LogicException
	 */
	public function canNotGetStrategy() {
		$this->repository->getStrategy ('unknownStrategy');
	}
}