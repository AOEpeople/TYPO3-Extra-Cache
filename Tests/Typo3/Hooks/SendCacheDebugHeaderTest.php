<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2010 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once dirname ( __FILE__ ) . '/../../AbstractTestcase.php';

/**
 * Test case for Tx_Extracache_Typo3_Hooks_SendCacheDebugHeader
 *
 * @package extracache_tests
 * @subpackage Typo3_Hooks
 */
class Tx_Extracache_Typo3_Hooks_SendCacheDebugHeaderTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var Tx_Extracache_Configuration_ExtensionManager
	 */
	private $extensionManager;
	/**
	 * @var Tx_Extracache_Typo3_Hooks_SendCacheDebugHeader
	 */
	private $hook;
	/**
	 * @var tslib_fe
	 */
	private $tsfeMock;
	
	/**
	 * Sets up this test case.
	 */
	protected function setUp() {
		$this->loadClass('Tx_Extracache_Configuration_ExtensionManager');
		$this->loadClass('Tx_Extracache_Typo3_Hooks_SendCacheDebugHeader');
		$this->extensionManager = $this->getMock('Tx_Extracache_Configuration_ExtensionManager', array(), array(), '', FALSE);
		$this->hook = $this->getMock('Tx_Extracache_Typo3_Hooks_SendCacheDebugHeader', array('getExtensionManager','sendHttpRequestHeader'));
		$this->hook->expects($this->any())->method('getExtensionManager')->will($this->returnValue( $this->extensionManager ));
		$this->tsfeMock = $this->getMock('tslib_fe', array(), array(), '', FALSE);
	}

	/**
	 * Cleans this test case
	 */
	protected function tearDown() {
		unset($this->extensionManager);
		unset($this->hook);
		unset($this->tsfeMock);
	}
	/**
	 * @test
	 */
	public function dontSendHttpRequestHeader() {
		$this->extensionManager->expects($this->once())->method('get')->with('developmentContext')->will($this->returnValue( 0 ));
		$this->hook->expects($this->never())->method('sendHttpRequestHeader');
		$this->tsfeMock->expects($this->never())->method('isINTincScript');
		$this->tsfeMock->expects($this->never())->method('isEXTincScript');
		$this->hook->sendCacheDebugHeader($this->tsfeMock);
	}
	/**
	 * @test
	 */
	public function sendHttpRequestHeader() {
		// page contains INTincScript
		$this->extensionManager->expects($this->once())->method('get')->with('developmentContext')->will($this->returnValue( 1 ));
		$this->hook->expects($this->once())->method('sendHttpRequestHeader')->with( array('INT') );
		$this->tsfeMock->expects($this->once())->method('isINTincScript')->will($this->returnValue( TRUE ));
		$this->hook->sendCacheDebugHeader($this->tsfeMock);

		// page contains EXTincScript
		$this->setUp();
		$this->extensionManager->expects($this->once())->method('get')->with('developmentContext')->will($this->returnValue( 1 ));
		$this->hook->expects($this->once())->method('sendHttpRequestHeader')->with( array('EXT') );
		$this->tsfeMock->expects($this->once())->method('isEXTincScript')->will($this->returnValue( TRUE ));
		$this->hook->sendCacheDebugHeader($this->tsfeMock);

		// no-cache-flag is set
		$this->setUp();
		$this->tsfeMock->no_cache = 1;
		$this->extensionManager->expects($this->once())->method('get')->with('developmentContext')->will($this->returnValue( 1 ));
		$this->hook->expects($this->once())->method('sendHttpRequestHeader')->with( array('no_cache') );
		$this->hook->sendCacheDebugHeader($this->tsfeMock);

		// cacheContentFlag is set
		$this->setUp();
		$this->tsfeMock->cacheContentFlag = 1;
		$this->extensionManager->expects($this->once())->method('get')->with('developmentContext')->will($this->returnValue( 1 ));
		$this->hook->expects($this->once())->method('sendHttpRequestHeader')->with( array('cached') );
		$this->hook->sendCacheDebugHeader($this->tsfeMock);
	}
}