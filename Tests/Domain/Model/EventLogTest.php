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

/**
 * test case for Tx_Extracache_Domain_Model_EventLog
 * @package extracache_tests
 * @subpackage Domain_Model
 */
class Tx_Extracache_Domain_Model_EventLogTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var Tx_Extracache_Domain_Model_Event
	 */
	private $event;
	/**
	 * 
	 * @var Tx_Extracache_Domain_Model_EventLog
	 */
	private $eventLog;
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->event = $this->getMock ( 'Tx_Extracache_Domain_Model_Event', array(), array(), '', FALSE);
		$this->eventLog = new Tx_Extracache_Domain_Model_EventLog( $this->event );
		$this->eventLog->setStopTime();
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->event );
		unset ( $this->eventLog );
	}
	/**
	 * @test
	 */
	public function getFunctions() {
		$this->assertTrue( $this->eventLog->getEvent() === $this->event );
		$this->assertTrue( $this->eventLog->getStartTime() > 0 );
		$this->assertTrue( $this->eventLog->getStopTime() > 0 );
		$this->assertTrue( is_array( $this->eventLog->getInfos()) );
		$this->assertTrue( count($this->eventLog->getInfos()) === 0 );

		// add info-object
		$info = $this->getMock ( 'Tx_Extracache_Domain_Model_Info', array(), array(), '', FALSE);
		$this->eventLog->addInfo( $info );
		$infos = $this->eventLog->getInfos();
		$this->assertTrue( count($infos) === 1 );
		$this->assertTrue( $infos[0] === $info );
	}
}