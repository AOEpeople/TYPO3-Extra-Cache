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
	 * @var Tx_Extbase_Utility_ClassLoader
	 */
	private $classLoader;

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
	 * @string
	 */
	protected function loadClass($className) {
		$this->getClassLoader()->loadClass( $className );
	}

	/**
	 * @return Tx_Extbase_Utility_ClassLoader
	 */
	private function getClassLoader() {
		if($this->classLoader === NULL) {
			$this->classLoader = new Tx_Extbase_Utility_ClassLoader();
		}
		return $this->classLoader;
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