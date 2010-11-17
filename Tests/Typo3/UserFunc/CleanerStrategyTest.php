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
 * test case for Tx_Extracache_Typo3_UserFunc_CleanerStrategy
 * @package extracache_tests
 * @subpackage Typo3_UserFunc
 */
class Tx_Extracache_Typo3_UserFunc_CleanerStrategyTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var array
	 */
	private $cleanerStrategies;
	/**
	 * @var Tx_Extracache_Typo3_UserFunc_CleanerStrategy
	 */
	private $userFunc;
	/**
	 * @var Tx_Extracache_Domain_Repository_CleanerStrategyRepository
	 */
	private $mockedCleanerStrategyRepository;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->loadClass('Tx_Extracache_Typo3_UserFunc_CleanerStrategy');
		$this->loadClass('Tx_Extracache_Domain_Repository_CleanerStrategyRepository');
		
		$this->cleanerStrategies = array();
		$this->cleanerStrategies[] =  t3lib_div::makeInstance('Tx_Extracache_Domain_Model_CleanerStrategy', 1, NULL, NULL, 'key1', 'aaaaa');
		$this->cleanerStrategies[] =  t3lib_div::makeInstance('Tx_Extracache_Domain_Model_CleanerStrategy', 1, NULL, NULL, 'key2', 'ccccc');
		$this->cleanerStrategies[] =  t3lib_div::makeInstance('Tx_Extracache_Domain_Model_CleanerStrategy', 1, NULL, NULL, 'key3', 'BBBBB');
		
		$this->mockedCleanerStrategyRepository = $this->getMock ( 'Tx_Extracache_Domain_Repository_CleanerStrategyRepository', array(), array(), '', FALSE);
		$this->mockedCleanerStrategyRepository->expects($this->any())->method('getAllStrategies')->will ( $this->returnValue ( $this->cleanerStrategies ) );
		$this->userFunc = $this->getMock ( 'Tx_Extracache_Typo3_UserFunc_CleanerStrategy', array('getCleanerStrategyRepository'));
		$this->userFunc->expects($this->any())->method('getCleanerStrategyRepository')->will ( $this->returnValue ( $this->mockedCleanerStrategyRepository ) );
		
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->userFunc );
		unset ( $this->mockedCleanerStrategyRepository );
	}
	/**
	 * test method getEventItemsProcFunc
	 * @test
	 */
	public function getEventItemsProcFunc() {
		$parameters = array();
		$parameters['items'] = array();
		$this->userFunc->getCleanerStrategyItemsProcFunc($parameters);
		
		$this->assertTrue( count($parameters['items']) === 3);
		$this->assertTrue( $parameters['items'][0][0] === 'aaaaa');
		$this->assertTrue( $parameters['items'][0][1] === 'key1');
		$this->assertTrue( $parameters['items'][1][0] === 'BBBBB');
		$this->assertTrue( $parameters['items'][1][1] === 'key3');
		$this->assertTrue( $parameters['items'][2][0] === 'ccccc');
		$this->assertTrue( $parameters['items'][2][1] === 'key2');
	}
}