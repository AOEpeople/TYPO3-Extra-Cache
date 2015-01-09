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
 * test case for Tx_Extracache_System_Event_Dispatcher
 * @package extracache_tests
 * @subpackage System_Event
 */
class Tx_Extracache_System_Event_DispatcherTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var Tx_Extracache_System_Event_Dispatcher
	 */
	private $eventDispatcher;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->eventDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_Extracache_System_Event_Dispatcher');
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->eventDispatcher );
	}

	/**
	 * if it can dispatch simple event
	 * @test
	 */
	public function canDispatchSimpleEvent() {
		$eventHandlerMock = $this->getMock('Tx_Extracache_SomeHandler',array('handleEvent'));
		$eventHandlerMock->expects($this->once())->method('handleEvent');

		$this->eventDispatcher->addHandler('myEvent', $eventHandlerMock, 'handleEvent');
		$event = $this->eventDispatcher->triggerEvent('myEvent', $this, array('eventinfo'=>1));
		$this->assertEquals($event->getInfos(), array('eventinfo'=>1));
	}
}