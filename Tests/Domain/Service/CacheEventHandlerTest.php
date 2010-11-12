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
	 * @var Tx_Extracache_Domain_Repository_EventRepository
	 */
	private $mockedEventRepository;
	/**
	 * @var Tx_Extracache_Persistence_Typo3DbBackend
	 */
	private $mockedTypo3DbBackend;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->loadClass('Tx_Extracache_Domain_Model_CleanerStrategy');
		$this->loadClass('Tx_Extracache_Domain_Service_CacheCleaner');
		$this->loadClass('Tx_Extracache_Domain_Service_CacheEventHandler');
		$this->loadClass('Tx_Extracache_Domain_Repository_CleanerStrategyRepository');
		$this->loadClass('Tx_Extracache_Domain_Repository_EventRepository');
		$this->loadClass('Tx_Extracache_Persistence_Typo3DbBackend');
		
		$this->mockedCacheCleaner = $this->getMock ( 'Tx_Extracache_Domain_Service_CacheCleaner', array(), array(), '', FALSE);
		$this->mockedCleanerStrategyRepository = $this->getMock ( 'Tx_Extracache_Domain_Repository_CleanerStrategyRepository', array(), array(), '', FALSE);
		$this->mockedEventRepository = $this->getMock ( 'Tx_Extracache_Domain_Repository_EventRepository', array(), array(), '', FALSE);
		$this->mockedTypo3DbBackend = $this->getMock ( 'Tx_Extracache_Persistence_Typo3DbBackend', array(), array(), '', FALSE);

		$this->cacheEventHandler = $this->getMock ( 'Tx_Extracache_Domain_Service_CacheEventHandler', array('createCacheCleaner','getCleanerStrategyRepository','getEventRepository','getTypo3DbBackend'), array(), '', FALSE);
		$this->cacheEventHandler->expects ( $this->any () )->method ( 'createCacheCleaner' )->will ( $this->returnValue ( $this->mockedCacheCleaner ) );
		$this->cacheEventHandler->expects ( $this->any () )->method ( 'getCleanerStrategyRepository' )->will ( $this->returnValue ( $this->mockedCleanerStrategyRepository ) );
		$this->cacheEventHandler->expects ( $this->any () )->method ( 'getEventRepository' )->will ( $this->returnValue ( $this->mockedEventRepository ) );
		$this->cacheEventHandler->expects ( $this->any () )->method ( 'getTypo3DbBackend' )->will ( $this->returnValue ( $this->mockedTypo3DbBackend ) );
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->mockedCacheCleaner );
		unset ( $this->mockedCleanerStrategyRepository );
		unset ( $this->mockedEventRepository );
		unset ( $this->mockedTypo3DbBackend );
		unset ( $this->cacheEventHandler );
	}

	/**
	 * test method handleEvent
	 * @test
	 */
	public function canHandleEvent() {
		$mockedCleanerStrategy = $this->getMock ( 'Tx_Extracache_Domain_Model_CleanerStrategy', array(), array(), '', FALSE);
		$pagesWithCacheCleanerStrategy = array();
		$pagesWithCacheCleanerStrategy[] = array('tx_extracache_cleanerstrategies' => 'test_strategy', 'uid' => '11');
		$this->mockedEventRepository->expects ( $this->once () )->method ( 'hasEvent' )->will ( $this->returnValue ( TRUE ) );
		$this->mockedTypo3DbBackend->expects ( $this->once () )->method ( 'getPagesWithCacheCleanerStrategyForEvent' )->will ( $this->returnValue ( $pagesWithCacheCleanerStrategy ) );
		$this->mockedCleanerStrategyRepository->expects ( $this->once () )->method ( 'hasStrategy' )->with('test_strategy')->will ( $this->returnValue ( TRUE ) );
		$this->mockedCleanerStrategyRepository->expects ( $this->once () )->method ( 'getStrategy' )->with('test_strategy')->will ( $this->returnValue ( $mockedCleanerStrategy ) );
		$this->mockedCacheCleaner->expects ( $this->once () )->method ( 'addCleanerInstruction' )->with($mockedCleanerStrategy,11);
		$this->mockedCacheCleaner->expects ( $this->once () )->method ( 'process' );		
		$this->cacheEventHandler->handleEvent('knownEvent');
	}
	/**
	 * test method handleEvent
	 * @test
	 * @expectedException RuntimeException
	 */
	public function canNotHandleEvent() {
		$this->mockedEventRepository->expects ( $this->once () )->method ( 'hasEvent' )->will ( $this->returnValue ( FALSE ) );
		$this->cacheEventHandler->handleEvent('unknownEvent');
	}
}