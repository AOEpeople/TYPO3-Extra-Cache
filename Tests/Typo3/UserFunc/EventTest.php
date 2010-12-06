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
 * test case for Tx_Extracache_Typo3_UserFunc_Event
 * @package extracache_tests
 * @subpackage Typo3_UserFunc
 */
class Tx_Extracache_Typo3_UserFunc_EventTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var array
	 */
	private $events;
	/**
	 * @var Tx_Extracache_Typo3_UserFunc_Event
	 */
	private $userFunc;
	/**
	 * @var Tx_Extracache_Domain_Repository_EventRepository
	 */
	private $mockedEventRepository;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->loadClass('Tx_Extracache_Typo3_UserFunc_Event');
		$this->loadClass('Tx_Extracache_Domain_Repository_EventRepository');
		
		$this->events = array();
		$this->events[] =  t3lib_div::makeInstance('Tx_Extracache_Domain_Model_Event', 'key1', 'aaaaa', 0);
		$this->events[] =  t3lib_div::makeInstance('Tx_Extracache_Domain_Model_Event', 'key2', 'ccccc', 0);
		$this->events[] =  t3lib_div::makeInstance('Tx_Extracache_Domain_Model_Event', 'key3', 'BBBBB', 0);
		
		$this->mockedEventRepository = $this->getMock ( 'Tx_Extracache_Domain_Repository_EventRepository', array(), array(), '', FALSE);
		$this->mockedEventRepository->expects($this->any())->method('getEvents')->will ( $this->returnValue ( $this->events ) );
		$this->userFunc = $this->getMock ( 'Tx_Extracache_Typo3_UserFunc_Event', array('getEventRepository'));
		$this->userFunc->expects($this->any())->method('getEventRepository')->will ( $this->returnValue ( $this->mockedEventRepository ) );
		
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->userFunc );
		unset ( $this->mockedEventRepository );
	}
	/**
	 * test method getEventItemsProcFunc
	 * @test
	 */
	public function getEventItemsProcFunc() {
		$parameters = array();
		$parameters['items'] = array();
		$this->userFunc->getEventItemsProcFunc($parameters);
		
		$this->assertTrue( count($parameters['items']) === 3);
		$this->assertTrue( $parameters['items'][0][0] === 'aaaaa');
		$this->assertTrue( $parameters['items'][0][1] === 'key1');
		$this->assertTrue( $parameters['items'][1][0] === 'BBBBB');
		$this->assertTrue( $parameters['items'][1][1] === 'key3');
		$this->assertTrue( $parameters['items'][2][0] === 'ccccc');
		$this->assertTrue( $parameters['items'][2][1] === 'key2');
	}
}