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
 * Test case for tx_Extracache_Typo3_Hooks_StaticFileCache_DirtyPagesHook
 *
 * @package extracache_tests
 * @subpackage Typo3_Hooks_StaticFileCache
 */
class Tx_Extracache_Typo3_Hooks_StaticFileCache_DirtyPagesHookTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var tx_Extracache_Typo3_Hooks_StaticFileCache_DirtyPagesHook
	 */
	private $dirtyPagesHook;
	/**
	 * @var Tx_Extracache_System_Event_Dispatcher
	 */
	private $eventDispatcher;
	/**
	 * @var Tx_Extracache_Configuration_ExtensionManager
	 */
	private $extensionManager;
	/**
	 * @var tx_ncstaticfilecache
	 */
	private $staticFileCache;
	/**
	 * @var Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	private $typo3DbBackend;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();

		$this->staticFileCache = $this->getMock('tx_ncstaticfilecache', array('getCacheDirectory'), array(), '', FALSE);
		$this->typo3DbBackend = $this->getMock('Tx_Extracache_System_Persistence_Typo3DbBackend', array(), array(), '', FALSE);
		$this->eventDispatcher = $this->getMock('Tx_Extracache_System_Event_Dispatcher', array(), array(), '', FALSE);
		$this->eventDispatcher->expects($this->any())->method('triggerEvent')->will($this->returnCallback(array($this, 'triggeredEventCallback')));
		$this->extensionManager = $this->getMock('Tx_Extracache_Configuration_ExtensionManager', array(), array(), '', FALSE);
		$this->extensionManager->expects($this->any())->method('isSupportForFeUsergroupsSet')->will($this->returnValue(TRUE));

		$this->dirtyPagesHook = $this->getMock(
			'tx_Extracache_Typo3_Hooks_StaticFileCache_DirtyPagesHook',
			array('getArgumentRepository', 'fetchUrl', 'getFileModificationTime', 'getEventDispatcher', 'getExtensionManager', 'getTypo3DbBackend')
		);
		$this->dirtyPagesHook->expects($this->never())->method('getArgumentRepository');
		$this->dirtyPagesHook->expects($this->any())->method('getEventDispatcher')->will($this->returnValue($this->eventDispatcher));
		$this->dirtyPagesHook->expects($this->any())->method('getExtensionManager')->will($this->returnValue($this->extensionManager));
		$this->dirtyPagesHook->expects($this->any())->method('getTypo3DbBackend')->will($this->returnValue($this->typo3DbBackend));
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		parent::tearDown();

		unset($this->staticFileCache);
		unset($this->dirtyPagesHook);
		unset($this->eventDispatcher);
        unset($this->typo3DbBackend);
	}

	/**
	 * Test method 'isProcessingDirtyPages'
	 * @return void
	 * @test
	 */
	public function isProcessingDirtyPages() {
		$originalServerArray = $_SERVER;
		$_SERVER['HTTP_X_PROCESS_DIRTY_PAGES'] = '0';
		$this->assertFalse( $this->dirtyPagesHook->isProcessingDirtyPages() );
		$_SERVER['HTTP_X_PROCESS_DIRTY_PAGES'] = '1';
		$this->assertTrue( $this->dirtyPagesHook->isProcessingDirtyPages() );
		$_SERVER = $originalServerArray;
	}
	/**
	 * Test method 'process'
	 * @test
	 */
	public function process_areDirtyPagesWithAnonymousGroupProcessed() {
		$this->typo3DbBackend->expects($this->never())->method('deleteQuery');
		$dirtyElement = array ();
		$dirtyElement[tx_Extracache_Typo3_Hooks_StaticFileCache_DirtyPagesHook::FIELD_GroupList] = '0,-1';
		$parameters = array ();
		$parameters ['dirtyElement'] = $dirtyElement;
		$this->dirtyPagesHook->process($parameters, $this->staticFileCache);
	}
	/**
	 * Test method 'process'
	 * @test
	 */
	public function process_arePagesWithGroupProcessed() {
		$this->typo3DbBackend->expects($this->once())->method('fullQuoteStr')->with('0,-5','cache_pages')->will($this->returnValue('\'0,-5\''));
		$this->typo3DbBackend->expects($this->once())->method('deleteQuery')->with( 'cache_pages', 'tx_extracache_grouplist=\'0,-5\' AND page_id=0' );
		$dirtyElement = array ();
		$dirtyElement[tx_Extracache_Typo3_Hooks_StaticFileCache_DirtyPagesHook::FIELD_GroupList] = '0,-5';
		$dirtyElement['pid'] = 0;
		$parameters = array ();
		$parameters['dirtyElement'] = $dirtyElement;
		$this->dirtyPagesHook->process($parameters, $this->staticFileCache);
	}
	/**
	 * Test method 'process'
	 * @test
	 */
	public function process_isExecutionCancelled() {
		$this->dirtyPagesHook->expects($this->at(3))->method('getFileModificationTime')->will($this->returnValue(10));
		$this->dirtyPagesHook->expects($this->at(4))->method('fetchUrl');
		$this->dirtyPagesHook->expects($this->at(5))->method('getFileModificationTime')->will($this->returnValue(11));
		$dirtyElement = array ();
		$dirtyElement[tx_Extracache_Typo3_Hooks_StaticFileCache_DirtyPagesHook::FIELD_GroupList] = '0,-1';
		$dirtyElement['pid'] = 0;
		$parameters = array ();
		$cancelExecution = FALSE;
		$parameters['dirtyElement'] = $dirtyElement;
		$parameters['cancelExecution'] = &$cancelExecution;
		$this->dirtyPagesHook->process($parameters, $this->staticFileCache);
		$this->assertTrue($cancelExecution);
	}
	/**
	 * Test method 'process'
	 * @test
	 */
	public function process_isExecutionNotCancelledIfFetchingFailed() {
		$this->dirtyPagesHook->expects($this->once())->method('fetchUrl');
		$this->dirtyPagesHook->expects($this->exactly(2))->method('getFileModificationTime')->will($this->returnValue(10));
		$dirtyElement = array();
		$dirtyElement[tx_Extracache_Typo3_Hooks_StaticFileCache_DirtyPagesHook::FIELD_GroupList] = '0,-1';
		$cancelExecution = FALSE;
		$parameters = array ();
		$parameters['dirtyElement'] = $dirtyElement;
		$parameters['cancelExecution'] = &$cancelExecution;
		$this->dirtyPagesHook->process($parameters, $this->staticFileCache);
		$this->assertFalse($cancelExecution);
	}
	/**
	 * Test method 'process'
	 * @return void
	 * @test
	 */
	public function process_isEventTriggeredOnProcessingDirtyPages() {
		$dirtyElement = array();
		$dirtyElement[tx_Extracache_Typo3_Hooks_StaticFileCache_DirtyPagesHook::FIELD_GroupList] = '0,-1';
		$parameters = array ();
		$parameters['dirtyElement'] = $dirtyElement;

		$this->dirtyPagesHook->process($parameters, $this->staticFileCache);

		$this->assertEquals(1, count($this->triggeredEvents));
		$this->assertInstanceOf('Tx_Extracache_System_Event_Events_EventOnStaticFileCache', $this->triggeredEvents[0]);
		$this->assertEquals(tx_Extracache_Typo3_Hooks_StaticFileCache_DirtyPagesHook::EVENT_Process, $this->triggeredEvents[0]->getName());
	}
}