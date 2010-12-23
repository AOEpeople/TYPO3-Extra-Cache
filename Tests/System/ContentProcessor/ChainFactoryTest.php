<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2010 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once dirname ( __FILE__ ) . '/../../AbstractTestcase.php';

/**
 * test case for Tx_Extracache_System_ContentProcessor_ChainFactory
 * @package extracache_tests
 * @subpackage System_Event
 */
class Tx_Extracache_System_ContentProcessor_ChainFactoryTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var Tx_Extracache_System_ContentProcessor_Chain
	 */
	private $mockedChain;
	
	/**
	 * @var Tx_Extracache_System_ContentProcessor_ChainFactory
	 */
	private $chainFactory;
	/**
	 * @var array
	 */
	private $contentProcessorDefinitions;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->mockedChain = $this->getMock ( 'Tx_Extracache_System_ContentProcessor_Chain', array (), array (), '', FALSE );

		$className = 'Tx_Extracache_System_ContentProcessor_Fixtures_DummyContentProcessor';
		$path = dirname ( __FILE__ ) . '/Fixtures/DummyContentProcessor.php';
		$this->contentProcessorDefinitions = array();
		$this->contentProcessorDefinitions[] = t3lib_div::makeInstance('Tx_Extracache_Domain_Model_ContentProcessorDefinition', $className, $path);

		$this->chainFactory = $this->getMock('Tx_Extracache_System_ContentProcessor_ChainFactory',array('createChain','getContentProcessorDefinitions'));
		$this->chainFactory->expects ( $this->any () )->method ( 'createChain' )->will ( $this->returnValue ( $this->mockedChain ) );
		$this->chainFactory->expects ( $this->any () )->method ( 'getContentProcessorDefinitions' )->will ( $this->returnValue ( $this->contentProcessorDefinitions ) );
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->chainFactory );
	}

	/**
	 * Test method getInitialisedChain
	 * @test
	 */
	public function getInitialisedChain() {
		$this->mockedChain->expects ( $this->once () )->method ( 'addContentProcessor' );
		$chain = $this->chainFactory->getInitialisedChain();
		$this->assertEquals( $this->mockedChain, $chain );
	}
}