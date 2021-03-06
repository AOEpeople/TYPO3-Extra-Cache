<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2010 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

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
	 * @var TypoScriptFrontendController
	 */
	private $tsfeMock;

	/**
	 * Sets up this test case.
	 */
	protected function setUp() {

		$this->chain = $this->getMock('Tx_Extracache_System_ContentProcessor_Chain', array(), array(), '', FALSE);
		$this->extensionManager = $this->getMock('Tx_Extracache_Configuration_ExtensionManager', array(), array(), '', FALSE);
		$this->tsfeMock = $this->getMock('TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController', array(), array(), '', FALSE);

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
		$this->chain->expects($this->once())->method('process');
		$this->hook->executeContentProcessor(array(), $this->tsfeMock);
	}
	/**
	 * @test
	 * @expectedException RuntimeException
	 */
	public function executeContentProcessor_contentProcessorsAreEnabled_throwException() {
		$this->extensionManager->expects($this->once())->method('areContentProcessorsEnabled')->will($this->returnValue( TRUE ));
		$this->chain->expects($this->once())->method('process')->will ( $this->throwException(new RuntimeException('') ) );
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