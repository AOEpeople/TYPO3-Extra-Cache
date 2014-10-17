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
 * test case for Tx_Extracache_Domain_Service_CacheCleaner
 * @package extracache_tests
 * @subpackage Domain_Service
 */
class Tx_Extracache_Domain_Service_CacheCleanerTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 *
	 * @var Tx_Extracache_Domain_Service_CacheCleaner
	 */
	private $cacheCleaner;
	/**
	 * @var Tx_Extracache_Domain_Repository_CleanerInstructionRepository
	 */
	private $mockedCleanerInstructionRepository;
	/**
	 * @var tx_ncstaticfilecache
	 */
	private $mockedStaticFileCache;
	/**
	 * @var t3lib_TCEmain
	 */
	private $mockedTceMain;
	/**
	 * @var Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	private $mockedTypo3DbBackend;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {

		$this->mockedCleanerInstructionRepository = $this->getMock ( 'Tx_Extracache_Domain_Repository_CleanerInstructionRepository', array(), array(), '', FALSE);
		$this->mockedStaticFileCache = $this->getMock ( 'tx_ncstaticfilecache', array(), array(), '', FALSE);
		$this->mockedTceMain = $this->getMock ( 't3lib_TCEmain', array(), array(), '', FALSE);
		$this->mockedTypo3DbBackend = $this->getMock ( 'Tx_Extracache_System_Persistence_Typo3DbBackend', array(), array(), '', FALSE);

		$this->cacheCleaner = $this->getMock ( 'Tx_Extracache_Domain_Service_CacheCleaner', array ('getCleanerInstructionRepository','getStaticFileCache','getTceMain','getTypo3DbBackend'));
		$this->cacheCleaner->expects ( $this->any () )->method ( 'getCleanerInstructionRepository' )->will ( $this->returnValue ( $this->mockedCleanerInstructionRepository ) );
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->mockedCleanerInstructionRepository );
		unset ( $this->mockedStaticFileCache );
		unset ( $this->mockedTceMain );
		unset ( $this->mockedTypo3DbBackend );
		unset ( $this->cacheCleaner );
	}

	/**
	 * test method addCleanerInstruction
	 * @test
	 */
	public function addCleanerInstruction() {
		$this->cacheCleaner->expects ( $this->once () )->method ( 'getStaticFileCache' )->will ( $this->returnValue ( $this->mockedStaticFileCache ) );
		$this->cacheCleaner->expects ( $this->once () )->method ( 'getTceMain' )->will ( $this->returnValue ( $this->mockedTceMain ) );
		$this->cacheCleaner->expects ( $this->once () )->method ( 'getTypo3DbBackend' )->will ( $this->returnValue ( $this->mockedTypo3DbBackend ) );
		$this->mockedCleanerInstructionRepository->expects ( $this->once () )->method ( 'addCleanerInstruction' );

		$cleanerStrategy = $this->getMock ( 'Tx_Extracache_Domain_Model_CleanerStrategy', array(), array(), '', FALSE);
		$this->cacheCleaner->addCleanerInstruction($cleanerStrategy, 1);
	}
	/**
	 * test method process
	 * @test
	 */
	public function process() {
		$mockedCleanerInstruction = $this->getMock ( 'Tx_Extracache_Domain_Model_CleanerInstruction', array(), array(), '', FALSE);
		$mockedCleanerInstruction->expects ( $this->once () )->method ( 'process' );
		$this->mockedCleanerInstructionRepository->expects ( $this->once () )->method ( 'getCleanerInstructions' )->will ( $this->returnValue ( array($mockedCleanerInstruction) ) );
		$this->cacheCleaner->process();
	}
}