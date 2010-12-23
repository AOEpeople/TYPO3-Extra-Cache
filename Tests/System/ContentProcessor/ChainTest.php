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
require_once dirname ( __FILE__ ) . '/Fixtures/DummyContentProcessor.php';

/**
 * test case for Tx_Extracache_System_ContentProcessor_Chain
 * @package extracache_tests
 * @subpackage System_Event
 */
class Tx_Extracache_System_ContentProcessor_ChainTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var Tx_Extracache_System_ContentProcessor_Chain
	 */
	private $chain;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$contentProcessor = new Tx_Extracache_System_ContentProcessor_Fixtures_DummyContentProcessor();
		$this->chain = $this->getMock('Tx_Extracache_System_ContentProcessor_Chain',array('crawlerIsRunning'));
		$this->chain->addContentProcessor( $contentProcessor );
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->chainFactory );
	}

	/**
	 * Test method process
	 * @test
	 */
	public function process_crawlerIsRunning() {
		$this->chain->expects ( $this->once () )->method ( 'crawlerIsRunning' )->will ( $this->returnValue ( TRUE ) );
		$this->assertEquals( $this->chain->process('test-content'), 'test-content' );
	}
	/**
	 * Test method process
	 * @test
	 */
	public function process_crawlerIsNotRunning() {
		$this->chain->expects ( $this->once () )->method ( 'crawlerIsRunning' )->will ( $this->returnValue ( FALSE ) );
		$this->assertEquals( $this->chain->process('test-content'), '[start]test-content[stop]' );
	}
}