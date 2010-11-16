<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2010 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Test case for Tx_Extracache_Typo3_Hooks_StaticFileCache_DirtyPagesHook
 *
 * @package extracache_tests
 * @subpackage Typo3_Hooks_StaticFileCache
 */
class Tx_Extracache_Typo3_Hooks_StaticFileCache_DirtyPagesHookTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var Tx_Extracache_Typo3_Hooks_StaticFileCache_DirtyPagesHook
	 */
	protected $dirtyPagesHook;

	/**
	 * @var tx_ncstaticfilecache
	 */
	protected $staticFileCache;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();

		$this->staticFileCache = $this->getMock('tx_ncstaticfilecache', array('getCacheDirectory'));

		$this->dirtyPagesHook = $this->getMock('Tx_Extracache_Typo3_Hooks_StaticFileCache_DirtyPagesHook', array('getArgumentRepository', 'fetchUrl', 'removeCaches', 'getFileModificationTime'));
		$this->dirtyPagesHook->expects($this->never())->method('getArgumentRepository');
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		parent::tearDown();

		unset($this->staticFileCache);
		unset($this->dirtyPagesHook);
	}

	/**
	 * test method processDirtyPages
	 * @test
	 */
	public function areDirtyPagesWithAnonymousGroupProcessed() {
		$this->dirtyPagesHook->expects($this->never())->method('removeCaches');
		$dirtyElement = array ();
		$dirtyElement[Tx_Extracache_Typo3_Hooks_StaticFileCache_DirtyPagesHook::FIELD_GroupList] = '0,-1';
		$parameters = array ();
		$parameters ['dirtyElement'] = $dirtyElement;
		$this->dirtyPagesHook->process($parameters, $this->staticFileCache);
	}
	/**
	 * @test
	 */
	public function arePagesWithGroupProcessed() {
		$this->dirtyPagesHook->expects($this->once())->method('removeCaches');
		$dirtyElement = array ();
		$dirtyElement[Tx_Extracache_Typo3_Hooks_StaticFileCache_DirtyPagesHook::FIELD_GroupList] = '0,-5';
		$dirtyElement['pid'] = 0;
		$parameters = array ();
		$parameters['dirtyElement'] = $dirtyElement;
		$this->dirtyPagesHook->process($parameters, $this->staticFileCache);
	}
	/**
	 * @test
	 */
	public function isExecutionCancelled() {
		$this->dirtyPagesHook->expects($this->once())->method('fetchUrl');
		$this->dirtyPagesHook->expects($this->at(0))->method('getFileModificationTime')->will($this->returnValue(10));
		$this->dirtyPagesHook->expects($this->at(2))->method('getFileModificationTime')->will($this->returnValue(11));
		$dirtyElement = array ();
		$dirtyElement[Tx_Extracache_Typo3_Hooks_StaticFileCache_DirtyPagesHook::FIELD_GroupList] = '0,-1';
		$dirtyElement['pid'] = 0;
		$parameters = array ();
		$cancelExecution = FALSE;
		$parameters['dirtyElement'] = $dirtyElement;
		$parameters['cancelExecution'] = &$cancelExecution;
		$this->dirtyPagesHook->process($parameters, $this->staticFileCache);
		$this->assertTrue($cancelExecution);
	}

	/**
	 * @test
	 */
	public function isExecutionNotCancelledIfFetchingFailed() {
		$this->dirtyPagesHook->expects($this->once())->method('fetchUrl');
		$this->dirtyPagesHook->expects($this->exactly(2))->method('getFileModificationTime')->will($this->returnValue(10));
		$dirtyElement = array();
		$dirtyElement[Tx_Extracache_Typo3_Hooks_StaticFileCache_DirtyPagesHook::FIELD_GroupList] = '0,-1';
		$cancelExecution = FALSE;
		$parameters = array ();
		$parameters['dirtyElement'] = $dirtyElement;
		$parameters['cancelExecution'] = &$cancelExecution;
		$this->dirtyPagesHook->process($parameters, $this->staticFileCache);
		$this->assertFalse($cancelExecution);
	}
}