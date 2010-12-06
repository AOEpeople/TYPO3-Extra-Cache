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
 * test case for Tx_Extracache_Domain_Model_Event
 * @package extracache_tests
 * @subpackage Domain_Model
 */
class Tx_Extracache_Domain_Model_EventTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * 
	 * @var Tx_Extracache_Domain_Model_Event
	 */
	private $event;
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->event = new Tx_Extracache_Domain_Model_Event('eventKey', 'eventName', 3600);
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->event );
	}
	/**
	 * @test
	 */
	public function getFunctions() {
		$this->assertTrue( $this->event->getInterval() === 3600 );
		$this->assertTrue( $this->event->getKey() === 'eventKey' );
		$this->assertTrue( $this->event->getName() === 'eventName' );
	}
}