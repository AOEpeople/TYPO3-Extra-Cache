<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2010 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once dirname ( __FILE__ ) . '/../../AbstractTestcase.php';

/**
 * test case for Tx_Extracache_Domain_Model_CleanerStrategy
 * @package extracache_tests
 * @subpackage Domain_Model
 */
class Tx_Extracache_Domain_Model_CleanerStrategyTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * 
	 * @var Tx_Extracache_Domain_Model_CleanerStrategy
	 */
	private $cleanerStrategy;
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->cleanerStrategy = new Tx_Extracache_Domain_Model_CleanerStrategy(Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_TYPO3Clear, Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenOnly, Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsOnly, 'cleanerStrategyKey', 'cleanerStrategyName');
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->cleanerStrategy );
	}
	/**
	 * @test
	 */
	public function getFunctions() {
		$this->assertTrue( $this->cleanerStrategy->getActions() === Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_TYPO3Clear );
		$this->assertTrue( $this->cleanerStrategy->getChildrenMode() === Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenOnly );
		$this->assertTrue( $this->cleanerStrategy->getElementsMode() === Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsOnly );
		$this->assertTrue( $this->cleanerStrategy->getKey() === 'cleanerStrategyKey' );
		$this->assertTrue( $this->cleanerStrategy->getName() === 'cleanerStrategyName' );
	}
	/**
	 * @test
	 */
	public function getSupportedChildModes() {
		$supportedChildrenModes = Tx_Extracache_Domain_Model_CleanerStrategy::getSupportedChildModes();
		$this->assertTrue( count($supportedChildrenModes) === 3 );
		$this->assertTrue( $supportedChildrenModes[0] ===  Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenWithParent);
		$this->assertTrue( $supportedChildrenModes[1] ===  Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenNoAction);
		$this->assertTrue( $supportedChildrenModes[2] ===  Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenOnly);
	}
	/**
	 * @test
	 */
	public function getSupportedElementModes() {
		$supportedElementModes = Tx_Extracache_Domain_Model_CleanerStrategy::getSupportedElementModes();
		$this->assertTrue( count($supportedElementModes) === 3 );
		$this->assertTrue( $supportedElementModes[0] ===  Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsWithParent);
		$this->assertTrue( $supportedElementModes[1] ===  Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsNoAction);
		$this->assertTrue( $supportedElementModes[2] ===  Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsOnly);
	}
}