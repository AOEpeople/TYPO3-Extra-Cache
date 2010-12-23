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
 * Test case for tx_Extracache_Typo3_Hooks_ExecuteContentProcessor
 *
 * @package extracache_tests
 * @subpackage Typo3_Hooks
 */
class Tx_Extracache_Typo3_Hooks_ExecuteContentProcessorTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var Tx_Extracache_System_ContentProcessor_Chain
	 */
	private $chain;
	/**
	 * @var Tx_Extracache_Configuration_ExtensionManager
	 */
	private $extensionManager;
	/**
	 * @var tx_Extracache_Typo3_Hooks_ExecuteContentProcessor
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
		$this->loadClass('Tx_Extracache_System_ContentProcessor_Chain');
		$this->loadClass('tx_Extracache_Typo3_Hooks_ExecuteContentProcessor');

		$this->chain = $this->getMock('Tx_Extracache_System_ContentProcessor_Chain', array(), array(), '', FALSE);
		$this->extensionManager = $this->getMock('Tx_Extracache_Configuration_ExtensionManager', array(), array(), '', FALSE);
		$this->tsfeMock = $this->getMock('tslib_fe', array(), array(), '', FALSE);

		$this->hook = $this->getMock('tx_Extracache_Typo3_Hooks_ExecuteContentProcessor', array('getContentProcessorChain','getExtensionManager'));
		$this->hook->expects($this->any())->method('getContentProcessorChain')->will($this->returnValue( $this->chain ));
		$this->hook->expects($this->any())->method('getExtensionManager')->will($this->returnValue( $this->extensionManager ));
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
	public function executeContentProcessor_contentProcessorsAreEnabled_dontThrowException() {
		// dont throw exception, because there is no exception
		$this->extensionManager->expects($this->once())->method('areContentProcessorsEnabled')->will($this->returnValue( TRUE ));
		$this->extensionManager->expects($this->any())->method('isDevelopmentContextSet')->will($this->returnValue( TRUE ));
		$this->chain->expects($this->once())->method('process');
		$this->hook->executeContentProcessor(array(), $this->tsfeMock);

		// dont throw exception, because developmentContext is set to FALSE
		$this->setUp();
		$this->extensionManager->expects($this->once())->method('areContentProcessorsEnabled')->will($this->returnValue( TRUE ));
		$this->extensionManager->expects($this->any())->method('isDevelopmentContextSet')->will($this->returnValue( FALSE ));
		$this->chain->expects($this->once())->method('process')->will ( $this->throwException(new Exception('') ) );
		$this->hook->executeContentProcessor(array(), $this->tsfeMock);
	}
	/**
	 * @test
	 * @expectedException Exception
	 */
	public function executeContentProcessor_contentProcessorsAreEnabled_throwException() {
		$this->extensionManager->expects($this->once())->method('areContentProcessorsEnabled')->will($this->returnValue( TRUE ));
		$this->extensionManager->expects($this->any())->method('isDevelopmentContextSet')->will($this->returnValue( TRUE ));
		$this->chain->expects($this->once())->method('process')->will ( $this->throwException(new Exception('') ) );
		$this->hook->executeContentProcessor(array(), $this->tsfeMock);
	}
	/**
	 * @test
	 */
	public function executeContentProcessor_contentProcessorsAreNotEnabled() {
		$this->extensionManager->expects($this->once())->method('areContentProcessorsEnabled')->will($this->returnValue( FALSE ));
		$this->chain->expects($this->never())->method('process');
		$this->hook->executeContentProcessor(array(), $this->tsfeMock);
	}
}