<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 AOE media GmbH <dev@aoemedia.de>
 *  All rights reserved
 *
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Abstract base class for extracache Tests
 *
 * @package extracache
 * @subpackage Tests
 */
abstract class Tx_Extracache_Tests_AbstractTestcase extends tx_phpunit_testcase {

	/**
	 * @var array
	 */
	protected $triggeredEvents = array();

	/**
	 * Sets up this test case.
	 *
	 * @return void
	 */
	protected function setUp() {
		$this->triggeredEvents = array();
	}

	/**
	 * Cleans up this test case.
	 *
	 * @return void
	 */
	protected function tearDown() {
		$this->triggeredEvents = array();
	}

	/**
	 * Local callback used to capture events. This can be seen as our
	 * local event listener that puts all events to $this->triggeredEvents.
	 *
	 * @param mixed $event
	 * @return mixed
	 */
	public function triggeredEventCallback($event) {
		$this->triggeredEvents[] = $event;
		return $event;
	}
}