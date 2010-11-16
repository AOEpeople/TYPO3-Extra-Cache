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
 * test case for Tx_Extracache_System_StaticCache_Response
 * @package extracache_tests
 * @subpackage System_StaticCache
 */
class Tx_Extracache_System_StaticCache_ResponseTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var Tx_Extracache_System_StaticCache_Response
	 */
	private $response;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->response = new Tx_Extracache_System_StaticCache_Response();
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->response );
	}
	
	/**
	 * Test get-methods
	 * @test
	 */
	public function getMethods() {
		$this->assertTrue( $this->response->setContent('test-content') === $this->response );
		$this->assertTrue( $this->response->getContent() === 'test-content' );
	}
}