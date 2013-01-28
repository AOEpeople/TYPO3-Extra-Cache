<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2013 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once dirname ( __FILE__ ) . '/../../AbstractTestcase.php';

/**
 * test case for Tx_Extracache_Domain_Service_CacheCleanerBuilder
 * @package extracache_tests
 * @subpackage Domain_Service
 */
class Tx_Extracache_Domain_Service_CacheCleanerBuilderTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var Tx_Extracache_Domain_Service_CacheCleanerBuilder
	 */
	private $builder;
	/**
	 * @var Tx_Extracache_Domain_Service_CacheCleaner
	 */
	private $mockedCacheCleaner;
	/**
	 * @var Tx_Extracache_Domain_Repository_CleanerStrategyRepository
	 */
	private $mockedCleanerStrategyRepository;

	/**
	 * set up the environment
	 */
	protected function setUp() {
		$this->loadClass('Tx_Extracache_Domain_Service_CacheCleaner');
		$this->loadClass('Tx_Extracache_Domain_Service_CacheCleanerBuilder');
		$this->loadClass('Tx_Extracache_Domain_Model_CleanerStrategy');
		$this->loadClass('Tx_Extracache_Domain_Repository_CleanerStrategyRepository');

		$this->mockedCacheCleaner = $this->getMock ( 'Tx_Extracache_Domain_Service_CacheCleaner', array(), array(), '', FALSE);
		$this->mockedCleanerStrategyRepository = $this->getMock ( 'Tx_Extracache_Domain_Repository_CleanerStrategyRepository', array(), array(), '', FALSE);

		$this->builder = $this->getMock ( 'Tx_Extracache_Domain_Service_CacheCleanerBuilder', array('createCacheCleaner', 'getCleanerStrategyRepository'), array(), '', FALSE);
		$this->builder->expects ( $this->any () )->method ( 'createCacheCleaner' )->will ( $this->returnValue ( $this->mockedCacheCleaner ) );
		$this->builder->expects ( $this->any () )->method ( 'getCleanerStrategyRepository' )->will ( $this->returnValue ( $this->mockedCleanerStrategyRepository ) );
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		parent::tearDown();
		unset ( $this->builder );
		unset ( $this->mockedCacheCleaner );
		unset ( $this->mockedCleanerStrategyRepository );
	}
	
	/**
	 * test method buildCacheCleanerForPage
	 * @test
	 */
	public function buildCacheCleanerForPage() {
		$page = array('uid' => '11', 'title' => 'testPage', 'tx_extracache_cleanerstrategies' => 'test_strategy' );
		$mockedCleanerStrategy = $this->getMock ( 'Tx_Extracache_Domain_Model_CleanerStrategy', array(), array(), '', FALSE);

		$this->mockedCleanerStrategyRepository->expects ( $this->once () )->method ( 'hasStrategy' )->with( $page['tx_extracache_cleanerstrategies'] )->will ( $this->returnValue ( TRUE ) );
		$this->mockedCleanerStrategyRepository->expects ( $this->once () )->method ( 'getStrategy' )->with( $page['tx_extracache_cleanerstrategies'] )->will ( $this->returnValue ( $mockedCleanerStrategy ) );
		$this->mockedCacheCleaner->expects ( $this->once () )->method ( 'addCleanerInstruction' )->with( $mockedCleanerStrategy, (integer) $page['uid'] );
		$this->builder->buildCacheCleanerForPage( $page );
	}
}