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
 * test case for Tx_Extracache_Validation_Validator_CleanerStrategy
 * @package extracache_tests
 * @subpackage Validation_Validator
 */
class Tx_Extracache_Validation_Validator_CleanerStrategyTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 *
	 * @var Tx_Extracache_Domain_Repository_CleanerStrategyRepository
	 */
	private $mockedCleanerStrategyRepository;
	/**
	 *
	 * @var Tx_Extracache_Validation_Validator_Event
	 */
	private $validator;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {

		$this->mockedCleanerStrategyRepository = $this->getMock ( 'Tx_Extracache_Domain_Repository_CleanerStrategyRepository', array (), array (), '', FALSE );
		$this->validator = $this->getMock ( 'Tx_Extracache_Validation_Validator_CleanerStrategy', array ('getCleanerStrategyRepository'));
		$this->validator->expects ( $this->any () )->method ( 'getCleanerStrategyRepository' )->will ( $this->returnValue ( $this->mockedCleanerStrategyRepository ) );
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->validator );
	}

	/**
	 * test method isValid
	 * @test
	 */
	public function actionsAreValid() {
		$this->mockedCleanerStrategyRepository->expects ( $this->any () )->method ( 'hasStrategy' )->will ( $this->returnValue ( FALSE ) );
		$cleanerStratey = $this->createCleanerStrategy(Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_TYPO3Clear + Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_StaticDirty, NULL, NULL);
		$this->assertTrue( $this->validator->isValid( $cleanerStratey ) );
		$cleanerStratey = $this->createCleanerStrategy(Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_StaticUpdate, NULL, NULL);
		$this->assertTrue( $this->validator->isValid( $cleanerStratey ) );
		$cleanerStratey = $this->createCleanerStrategy(Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_StaticClear, NULL, NULL);
		$this->assertTrue( $this->validator->isValid( $cleanerStratey ) );
		$cleanerStratey = $this->createCleanerStrategy(Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_StaticDirty, NULL, NULL);
		$this->assertTrue( $this->validator->isValid( $cleanerStratey ) );
		$cleanerStratey = $this->createCleanerStrategy(Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_TYPO3Clear, NULL, NULL);
		$this->assertTrue( $this->validator->isValid( $cleanerStratey ) );
	}
	/**
	 * test method isValid
	 * @test
	 */
	public function actionsAreNotValid() {
		$this->mockedCleanerStrategyRepository->expects ( $this->any () )->method ( 'hasStrategy' )->will ( $this->returnValue ( FALSE ) );
		$cleanerStratey = $this->createCleanerStrategy(0, NULL, NULL);
		$this->assertFalse( $this->validator->isValid( $cleanerStratey ) );
	}
	/**
	 * test method isValid
	 * @test
	 */
	public function childrenModeIsValid() {
		$this->mockedCleanerStrategyRepository->expects ( $this->any () )->method ( 'hasStrategy' )->will ( $this->returnValue ( FALSE ) );
		$cleanerStratey = $this->createCleanerStrategy(NULL, Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenWithParent, NULL);
		$this->assertTrue( $this->validator->isValid( $cleanerStratey ) );
		$cleanerStratey = $this->createCleanerStrategy(NULL, Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenNoAction, NULL);
		$this->assertTrue( $this->validator->isValid( $cleanerStratey ) );
		$cleanerStratey = $this->createCleanerStrategy(NULL, Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenOnly, NULL);
		$this->assertTrue( $this->validator->isValid( $cleanerStratey ) );
	}
	/**
	 * test method isValid
	 * @test
	 */
	public function childrenModeIsNotValid() {
		$this->mockedCleanerStrategyRepository->expects ( $this->any () )->method ( 'hasStrategy' )->will ( $this->returnValue ( FALSE ) );
		$cleanerStratey = $this->createCleanerStrategy(NULL, 'unknownChildrenMode', NULL);
		$this->assertFalse( $this->validator->isValid( $cleanerStratey ) );
	}
	/**
	 * test method isValid
	 * @test
	 */
	public function elementsModeIsValid() {
		$this->mockedCleanerStrategyRepository->expects ( $this->any () )->method ( 'hasStrategy' )->will ( $this->returnValue ( FALSE ) );
		$cleanerStratey = $this->createCleanerStrategy(NULL, NULL, Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsWithParent);
		$this->assertTrue( $this->validator->isValid( $cleanerStratey ) );
		$cleanerStratey = $this->createCleanerStrategy(NULL, NULL, Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsNoAction);
		$this->assertTrue( $this->validator->isValid( $cleanerStratey ) );
		$cleanerStratey = $this->createCleanerStrategy(NULL, NULL, Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsOnly);
		$this->assertTrue( $this->validator->isValid( $cleanerStratey ) );
	}
	/**
	 * test method isValid
	 * @test
	 */
	public function elementsModeIsNotValid() {
		$this->mockedCleanerStrategyRepository->expects ( $this->any () )->method ( 'hasStrategy' )->will ( $this->returnValue ( FALSE ) );
		$cleanerStratey = $this->createCleanerStrategy(NULL, NULL, 'unknownElementsMode');
		$this->assertFalse( $this->validator->isValid( $cleanerStratey ) );
	}
	/**
	 * test method isValid
	 * @test
	 */
	public function strategyIsNew() {
		$this->mockedCleanerStrategyRepository->expects ( $this->once () )->method ( 'hasStrategy' )->will ( $this->returnValue ( FALSE ) );
		$cleanerStratey = $this->createCleanerStrategy(NULL, NULL, NULL);
		$this->assertTrue( $this->validator->isValid( $cleanerStratey ) );
	}
	/**
	 * test method isValid
	 * @test
	 */
	public function strategyIsNotNew() {
		$this->mockedCleanerStrategyRepository->expects ( $this->once () )->method ( 'hasStrategy' )->will ( $this->returnValue ( TRUE ) );
		$cleanerStratey = $this->createCleanerStrategy(NULL, NULL, NULL);
		$this->assertFalse( $this->validator->isValid( $cleanerStratey ) );
	}

	/**
	 * @return Tx_Extracache_Domain_Model_Event
	 */
	private function createCleanerStrategy($actions, $childrenMode, $elementsMode) {
		// if parameter is NULL: is any correct value
		if($actions === NULL) {
			$actions = Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_TYPO3Clear;
		}
		if($childrenMode === NULL) {
			$childrenMode = Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenOnly;
		}
		if($elementsMode === NULL) {
			$elementsMode = Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsOnly;
		}
		return new Tx_Extracache_Domain_Model_CleanerStrategy($actions, $childrenMode, $elementsMode, 'keyname', 'name');
	}
}