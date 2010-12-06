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

require_once dirname ( __FILE__ ) . '/../AbstractTestcase.php';

/**
 * test case for Tx_Extracache_Typo3_SchedulerTaskProcessEventQueue
 * @package extracache_tests
 * @subpackage Typo3
 */
class Tx_Extracache_Typo3_SchedulerTaskProcessEventQueueTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var Tx_Extracache_Domain_Service_CacheEventHandler
	 */
	private $cacheEventHandler;
	/**
	 * @var Tx_Extracache_System_Event_Dispatcher
	 */
	private $eventDispatcher;
	/**
	 * @var Tx_Extracache_System_EventQueue
	 */
	private $eventQueue;
	/**
	 * @var Tx_Extracache_Typo3_SchedulerTaskCleanUpRemovedFiles
	 */
	private $task;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();
		$this->loadClass('Tx_Extracache_Domain_Service_CacheEventHandler');
		$this->loadClass('Tx_Extracache_System_Event_Dispatcher');
		$this->loadClass('Tx_Extracache_System_EventQueue');
		$this->loadClass('Tx_Extracache_Typo3_SchedulerTaskProcessEventQueue');

		$this->cacheEventHandler = $this->getMock ( 'Tx_Extracache_Domain_Service_CacheEventHandler', array(), array(), '', FALSE);
		$this->eventDispatcher = $this->getMock ( 'Tx_Extracache_System_Event_Dispatcher', array(), array(), '', FALSE);
		$this->eventDispatcher->expects($this->any())->method('triggerEvent')->will($this->returnCallback(array($this, 'triggeredEventCallback')));
		$this->eventQueue = $this->getMock ( 'Tx_Extracache_System_EventQueue', array(), array(), '', FALSE);

		$this->task = $this->getMock ( 'Tx_Extracache_Typo3_SchedulerTaskProcessEventQueue', array('getCacheEventHandler', 'getEventDispatcher', 'getEventQueue'));
		$this->task->expects($this->any())->method('getCacheEventHandler')->will ( $this->returnValue ( $this->cacheEventHandler ) );
		$this->task->expects($this->any())->method('getEventDispatcher')->will ( $this->returnValue ( $this->eventDispatcher ) );
		$this->task->expects($this->any())->method('getEventQueue')->will ( $this->returnValue ( $this->eventQueue ) );
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		parent::tearDown();
		unset ( $this->cacheEventHandler );
		unset ( $this->eventDispatcher );
		unset ( $this->eventQueue );
		unset ( $this->task );
	}

	/**
	 * Test method execute
	 * @test
	 */
	public function execute_withException() {
		$eventCacheKey = 'testEventKey';
		$this->eventQueue->expects ( $this->once () )->method ( 'getNextEventKeyForProcessing' )->will ( $this->returnValue ( $eventCacheKey ) );
		$this->cacheEventHandler->expects ( $this->once () )->method ( 'processCacheEvent' )->with( $eventCacheKey )->will ( $this->throwException(new Exception('') ) );
		$this->assertFalse( $this->task->execute() );
		$this->assertEquals( count($this->triggeredEvents), 1 );
		$this->assertEquals( $this->triggeredEvents[0], 'onProcessEventQueueError' );
	}
	/**
	 * Test method execute
	 * @test
	 */
	public function execute_withoutException() {
		$eventCacheKey = 'testEventKey';
		$this->eventQueue->expects ( $this->at (0) )->method ( 'getNextEventKeyForProcessing' )->will ( $this->returnValue ( $eventCacheKey ) );
		$this->cacheEventHandler->expects($this->once())->method('processCacheEvent')->with( $eventCacheKey );
		$this->eventQueue->expects ( $this->at (1) )->method ( 'deleteEventKey' )->with( $eventCacheKey );
		$this->eventQueue->expects ( $this->at (2) )->method ( 'getNextEventKeyForProcessing' )->will ( $this->returnValue ( NULL ) );
		$this->assertTrue( $this->task->execute() );
		$this->assertEquals( count($this->triggeredEvents), 0 );
	}
}