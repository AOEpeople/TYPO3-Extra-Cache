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
 * test case for Tx_Extracache_Domain_Service_CacheEventHandler
 * @package extracache_tests
 * @subpackage Domain_Service
 */
class Tx_Extracache_Domain_Service_CacheEventHandlerTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var Tx_Extracache_Domain_Service_CacheEventHandler
	 */
	private $cacheEventHandler;
	/**
	 * @var Tx_Extracache_Domain_Service_CacheCleaner
	 */
	private $mockedCacheCleaner;
	/**
	 * @var Tx_Extracache_Domain_Repository_CleanerStrategyRepository
	 */
	private $mockedCleanerStrategyRepository;
	/**
	 * @var Tx_Extracache_System_Event_Dispatcher
	 */
	private $mockedEventDispatcher;
	/**
	 * @var Tx_Extracache_System_EventQueue
	 */
	private $mockedEventQueue;
	/**
	 * @var Tx_Extracache_Domain_Repository_EventRepository
	 */
	private $mockedEventRepository;
	/**
	 * @var Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	private $mockedTypo3DbBackend;
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		parent::tearDown();
		unset ( $this->mockedCacheCleaner );
		unset ( $this->mockedCleanerStrategyRepository );
		unset ( $this->mockedEventDispatcher );
		unset ( $this->mockedEventQueue );
		unset ( $this->mockedEventRepository );
		unset ( $this->mockedTypo3DbBackend );
		unset ( $this->cacheEventHandler );
	}

	/**
	 * test method handleEvent
	 * @test
	 */
	public function canHandleEvent_cacheEventIntervallIsZero() {
		$this->createCacheEventHandler( TRUE );
		$cacheEvent = new Tx_Extracache_Domain_Model_Event('testEventKey', 'eventName', 0);
		$event = t3lib_div::makeInstance('Tx_Extracache_System_Event_Events_EventOnProcessCacheEvent', $cacheEvent->getKey());
		$this->mockedEventRepository->expects ( $this->once () )->method ( 'hasEvent' )->with( $cacheEvent->getKey() )->will ( $this->returnValue ( TRUE ) );
		$this->mockedEventRepository->expects ( $this->once () )->method ( 'getEvent' )->with( $cacheEvent->getKey() )->will ( $this->returnValue ( $cacheEvent ) );
		$this->cacheEventHandler->expects ( $this->once () )->method ( 'processCacheEvent' )->with( $cacheEvent->getKey() );
		$this->cacheEventHandler->handleEventOnProcessCacheEvent($event);
	}
	/**
	 * test method handleEvent
	 * @test
	 */
	public function canHandleEvent_cacheEventIntervallIsBiggerThanZero() {
		$this->createCacheEventHandler( TRUE );
		$cacheEvent = new Tx_Extracache_Domain_Model_Event('testEventKey', 'eventName', 3600);
		$event = t3lib_div::makeInstance('Tx_Extracache_System_Event_Events_EventOnProcessCacheEvent', $cacheEvent->getKey());
		$this->mockedEventRepository->expects ( $this->once () )->method ( 'hasEvent' )->with( $cacheEvent->getKey() )->will ( $this->returnValue ( TRUE ) );
		$this->mockedEventRepository->expects ( $this->once () )->method ( 'getEvent' )->with( $cacheEvent->getKey() )->will ( $this->returnValue ( $cacheEvent ) );
		$this->mockedEventQueue->expects ( $this->once () )->method ( 'addEvent' )->with( $cacheEvent );
		$this->cacheEventHandler->handleEventOnProcessCacheEvent($event);
	}
	/**
	 * test method handleEvent
	 * @test
	 * @expectedException RuntimeException
	 */
	public function canNotHandleEvent() {
		$this->createCacheEventHandler( TRUE );

		$event = t3lib_div::makeInstance('Tx_Extracache_System_Event_Events_EventOnProcessCacheEvent', 'unknownEvent');
		$this->mockedEventRepository->expects ( $this->once () )->method ( 'hasEvent' )->will ( $this->returnValue ( FALSE ) );
		$this->cacheEventHandler->handleEventOnProcessCacheEvent($event);
	}
	/**
	 * test method processCacheEvent
	 * @test
	 */
	public function canProcessCacheEvent_withoutExceptionWhileProcessing() {
		$this->createCacheEventHandler( FALSE );

		$eventKey = 'knownEventKey';
		$page1WithCacheCleanerStrategy = array('uid' => '11', 'title' => 'testPage', 'tx_extracache_cleanerstrategies' => 'test_strategy' );
		$pagesWithCacheCleanerStrategy = array();
		$pagesWithCacheCleanerStrategy[] = $page1WithCacheCleanerStrategy;
		$mockedCleanerStrategy = $this->getMock ( 'Tx_Extracache_Domain_Model_CleanerStrategy', array(), array(), '', FALSE);

		$this->mockedTypo3DbBackend->expects ( $this->once () )->method ( 'getPagesWithCacheCleanerStrategyForEvent' )->with( $eventKey )->will ( $this->returnValue ( $pagesWithCacheCleanerStrategy ) );
		$this->mockedCleanerStrategyRepository->expects ( $this->once () )->method ( 'hasStrategy' )->with( $page1WithCacheCleanerStrategy['tx_extracache_cleanerstrategies'] )->will ( $this->returnValue ( TRUE ) );
		$this->mockedCleanerStrategyRepository->expects ( $this->once () )->method ( 'getStrategy' )->with( $page1WithCacheCleanerStrategy['tx_extracache_cleanerstrategies'] )->will ( $this->returnValue ( $mockedCleanerStrategy ) );
		$this->mockedCacheCleaner->expects ( $this->once () )->method ( 'addCleanerInstruction' )->with( $mockedCleanerStrategy, (integer) $page1WithCacheCleanerStrategy['uid'] );
		$this->mockedCacheCleaner->expects ( $this->once () )->method ( 'process' );
		$this->cacheEventHandler->processCacheEvent( $eventKey );

		$this->assertEquals( count($this->triggeredEvents), 1 );
		$this->assertEquals( $this->triggeredEvents[0], 'onProcessCacheEventInfo' );
	}
	/**
	 * test method processCacheEvent
	 * @test
	 */
	public function canProcessCacheEvent_withExceptionWhileProcessing() {
		$this->createCacheEventHandler( FALSE );

		$eventKey = 'knownEventKey';
		$page1WithCacheCleanerStrategy = array('uid' => '11', 'title' => 'testPage', 'tx_extracache_cleanerstrategies' => 'test_strategy' );
		$pagesWithCacheCleanerStrategy = array();
		$pagesWithCacheCleanerStrategy[] = $page1WithCacheCleanerStrategy;
		$mockedCleanerStrategy = $this->getMock ( 'Tx_Extracache_Domain_Model_CleanerStrategy', array(), array(), '', FALSE);

		$this->mockedTypo3DbBackend->expects ( $this->once () )->method ( 'getPagesWithCacheCleanerStrategyForEvent' )->with( $eventKey )->will ( $this->returnValue ( $pagesWithCacheCleanerStrategy ) );
		$this->mockedCleanerStrategyRepository->expects ( $this->once () )->method ( 'hasStrategy' )->with( $page1WithCacheCleanerStrategy['tx_extracache_cleanerstrategies'] )->will ( $this->returnValue ( TRUE ) );
		$this->mockedCleanerStrategyRepository->expects ( $this->once () )->method ( 'getStrategy' )->with( $page1WithCacheCleanerStrategy['tx_extracache_cleanerstrategies'] )->will ( $this->returnValue ( $mockedCleanerStrategy ) );
		$this->mockedCacheCleaner->expects ( $this->once () )->method ( 'addCleanerInstruction' )->with( $mockedCleanerStrategy, (integer) $page1WithCacheCleanerStrategy['uid'] );
		$this->mockedCacheCleaner->expects ( $this->once () )->method ( 'process' )->will ( $this->throwException( new Exception('') ) );
		$this->cacheEventHandler->processCacheEvent( $eventKey );

		$this->assertEquals( count($this->triggeredEvents), 2 );
		$this->assertEquals( $this->triggeredEvents[0], 'onProcessCacheEventInfo' );
		$this->assertEquals( $this->triggeredEvents[1], 'onProcessCacheEventError' );
	}

	/**
	 * @param boolean $mockMethodProcessCacheEvent
	 */
	private function createCacheEventHandler($mockMethodProcessCacheEvent) {
		parent::setUp();
		$this->loadClass('Tx_Extracache_Domain_Model_CleanerStrategy');
		$this->loadClass('Tx_Extracache_Domain_Service_CacheCleaner');
		$this->loadClass('Tx_Extracache_Domain_Service_CacheEventHandler');
		$this->loadClass('Tx_Extracache_Domain_Repository_CleanerStrategyRepository');
		$this->loadClass('Tx_Extracache_System_Event_Dispatcher');
		$this->loadClass('Tx_Extracache_System_EventQueue');
		$this->loadClass('Tx_Extracache_Domain_Repository_EventRepository');
		$this->loadClass('Tx_Extracache_System_Persistence_Typo3DbBackend');

		$this->mockedCacheCleaner = $this->getMock ( 'Tx_Extracache_Domain_Service_CacheCleaner', array(), array(), '', FALSE);
		$this->mockedCleanerStrategyRepository = $this->getMock ( 'Tx_Extracache_Domain_Repository_CleanerStrategyRepository', array(), array(), '', FALSE);
		$this->mockedEventDispatcher = $this->getMock ( 'Tx_Extracache_System_Event_Dispatcher', array(), array(), '', FALSE);
		$this->mockedEventDispatcher->expects($this->any())->method('triggerEvent')->will($this->returnCallback(array($this, 'triggeredEventCallback')));
		$this->mockedEventQueue = $this->getMock ( 'Tx_Extracache_System_EventQueue', array(), array(), '', FALSE);
		$this->mockedEventRepository = $this->getMock ( 'Tx_Extracache_Domain_Repository_EventRepository', array(), array(), '', FALSE);
		$this->mockedTypo3DbBackend = $this->getMock ( 'Tx_Extracache_System_Persistence_Typo3DbBackend', array(), array(), '', FALSE);

		$mockMethods = array();
		$mockMethods[] = 'createCacheCleaner';
		$mockMethods[] = 'getCleanerStrategyRepository';
		$mockMethods[] = 'getEventDispatcher';
		$mockMethods[] = 'getEventQueue';
		$mockMethods[] = 'getEventRepository';
		$mockMethods[] = 'getTypo3DbBackend';
		if($mockMethodProcessCacheEvent === TRUE) {
			$mockMethods[] = 'processCacheEvent';
		}
		$this->cacheEventHandler = $this->getMock ( 'Tx_Extracache_Domain_Service_CacheEventHandler', $mockMethods, array(), '', FALSE);
		$this->cacheEventHandler->expects ( $this->any () )->method ( 'createCacheCleaner' )->will ( $this->returnValue ( $this->mockedCacheCleaner ) );
		$this->cacheEventHandler->expects ( $this->any () )->method ( 'getCleanerStrategyRepository' )->will ( $this->returnValue ( $this->mockedCleanerStrategyRepository ) );
		$this->cacheEventHandler->expects ( $this->any () )->method ( 'getEventDispatcher' )->will ( $this->returnValue ( $this->mockedEventDispatcher ) );
		$this->cacheEventHandler->expects ( $this->any () )->method ( 'getEventQueue' )->will ( $this->returnValue ( $this->mockedEventQueue ) );
		$this->cacheEventHandler->expects ( $this->any () )->method ( 'getEventRepository' )->will ( $this->returnValue ( $this->mockedEventRepository ) );
		$this->cacheEventHandler->expects ( $this->any () )->method ( 'getTypo3DbBackend' )->will ( $this->returnValue ( $this->mockedTypo3DbBackend ) );
	}
}