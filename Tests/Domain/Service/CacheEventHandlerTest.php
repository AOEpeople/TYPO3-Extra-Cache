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
	 * @var Tx_Extracache_Domain_Service_CacheCleanerBuilder
	 */
	private $mockedCacheCleanerBuilder;
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
		unset ( $this->mockedCacheCleanerBuilder );
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
		$this->cacheEventHandler->expects ( $this->once () )->method ( 'processCacheEvent' )->with( $cacheEvent );
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
		$event = $this->getMock ( 'Tx_Extracache_Domain_Model_Event', array(), array(), '', FALSE);
		$event->expects ( $this->any () )->method ( 'getKey' )->will ( $this->returnValue ( $eventKey ) );
		$event->expects ( $this->any () )->method ( 'getWriteLog' )->will ( $this->returnValue ( TRUE ) );
		$page = $this->getPageWithCacheCleanerStrategy();

		$this->mockedTypo3DbBackend->expects ( $this->once () )->method ( 'getPagesWithCacheCleanerStrategyForEvent' )->with( $eventKey )->will ( $this->returnValue ( array($page) ) );
		$this->mockedTypo3DbBackend->expects ( $this->once () )->method ( 'writeEventLog' );
		
		
		$mockedCacheCleaner = $this->getMock ( 'Tx_Extracache_Domain_Service_CacheCleaner', array(), array(), '', FALSE);
		$mockedCacheCleaner->expects ( $this->once () )->method ( 'process' );
		$this->mockedCacheCleanerBuilder->expects ( $this->once () )->method ( 'buildCacheCleanerForPage' )->with( $page )->will ( $this->returnValue ( $mockedCacheCleaner ) );
		$this->cacheEventHandler->processCacheEvent( $event );

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
		$event = $this->getMock ( 'Tx_Extracache_Domain_Model_Event', array(), array(), '', FALSE);
		$event->expects ( $this->any () )->method ( 'getKey' )->will ( $this->returnValue ( $eventKey ) );
		$event->expects ( $this->any () )->method ( 'getWriteLog' )->will ( $this->returnValue ( TRUE ) );
		$page = $this->getPageWithCacheCleanerStrategy();

		$this->mockedTypo3DbBackend->expects ( $this->once () )->method ( 'getPagesWithCacheCleanerStrategyForEvent' )->with( $eventKey )->will ( $this->returnValue ( array($page) ) );
		$this->mockedTypo3DbBackend->expects ( $this->once () )->method ( 'writeEventLog' );
		$mockedCacheCleaner = $this->getMock ( 'Tx_Extracache_Domain_Service_CacheCleaner', array(), array(), '', FALSE);
		$mockedCacheCleaner->expects ( $this->once () )->method ( 'process' )->will ( $this->throwException( new Exception('') ) );
		$this->mockedCacheCleanerBuilder->expects ( $this->once () )->method ( 'buildCacheCleanerForPage' )->with( $page )->will ( $this->returnValue ( $mockedCacheCleaner ) );
		$this->cacheEventHandler->processCacheEvent( $event );

		$this->assertEquals( count($this->triggeredEvents), 2 );
		$this->assertEquals( $this->triggeredEvents[0], 'onProcessCacheEventInfo' );
		$this->assertEquals( $this->triggeredEvents[1], 'onProcessCacheEventError' );
	}

	/**
	 * @param boolean $mockMethodProcessCacheEvent
	 */
	private function createCacheEventHandler($mockMethodProcessCacheEvent) {
		parent::setUp();
		
		$this->loadClass('Tx_Extracache_Domain_Service_CacheCleanerBuilder');
		$this->loadClass('Tx_Extracache_Domain_Service_CacheEventHandler');
		$this->loadClass('Tx_Extracache_System_Event_Dispatcher');
		$this->loadClass('Tx_Extracache_System_EventQueue');
		$this->loadClass('Tx_Extracache_Domain_Repository_EventRepository');
		$this->loadClass('Tx_Extracache_System_Persistence_Typo3DbBackend');

		$this->mockedCacheCleanerBuilder = $this->getMock ( 'Tx_Extracache_Domain_Service_CacheCleanerBuilder', array(), array(), '', FALSE);
		$this->mockedEventDispatcher = $this->getMock ( 'Tx_Extracache_System_Event_Dispatcher', array(), array(), '', FALSE);
		$this->mockedEventDispatcher->expects($this->any())->method('triggerEvent')->will($this->returnCallback(array($this, 'triggeredEventCallback')));
		$this->mockedEventQueue = $this->getMock ( 'Tx_Extracache_System_EventQueue', array(), array(), '', FALSE);
		$this->mockedEventRepository = $this->getMock ( 'Tx_Extracache_Domain_Repository_EventRepository', array(), array(), '', FALSE);
		$this->mockedTypo3DbBackend = $this->getMock ( 'Tx_Extracache_System_Persistence_Typo3DbBackend', array(), array(), '', FALSE);

		$mockMethods = array();
		$mockMethods[] = 'getCacheCleanerBuilder';
		$mockMethods[] = 'getEventDispatcher';
		$mockMethods[] = 'getEventQueue';
		$mockMethods[] = 'getEventRepository';
		$mockMethods[] = 'getTypo3DbBackend';
		if($mockMethodProcessCacheEvent === TRUE) {
			$mockMethods[] = 'processCacheEvent';
		}
		$this->cacheEventHandler = $this->getMock ( 'Tx_Extracache_Domain_Service_CacheEventHandler', $mockMethods, array(), '', FALSE);
		$this->cacheEventHandler->expects ( $this->any () )->method ( 'getCacheCleanerBuilder' )->will ( $this->returnValue ( $this->mockedCacheCleanerBuilder ) );
		$this->cacheEventHandler->expects ( $this->any () )->method ( 'getEventDispatcher' )->will ( $this->returnValue ( $this->mockedEventDispatcher ) );
		$this->cacheEventHandler->expects ( $this->any () )->method ( 'getEventQueue' )->will ( $this->returnValue ( $this->mockedEventQueue ) );
		$this->cacheEventHandler->expects ( $this->any () )->method ( 'getEventRepository' )->will ( $this->returnValue ( $this->mockedEventRepository ) );
		$this->cacheEventHandler->expects ( $this->any () )->method ( 'getTypo3DbBackend' )->will ( $this->returnValue ( $this->mockedTypo3DbBackend ) );
	}
	/**
	 * @return array
	 */
	private function getPageWithCacheCleanerStrategy() {
		$page = array();
		$page['uid'] = '11';
		$page['title'] = 'testPage';
		$page['tx_extracache_cleanerstrategies'] = 'test_strategy';
		return $page;		
	}
}