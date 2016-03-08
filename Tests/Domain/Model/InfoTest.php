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

/**
 * test case for Tx_Extracache_Domain_Model_Info
 * @package extracache_tests
 * @subpackage Domain_Model
 */
class Tx_Extracache_Domain_Model_InfoTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * 
	 * @var Tx_Extracache_Domain_Model_Info
	 */
	private $info;
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->info = new Tx_Extracache_Domain_Model_Info('test-title', Tx_Extracache_Domain_Model_Info::TYPE_notice);
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->info );
	}
	/**
	 * @test
	 */
	public function getFunctions() {
		$this->assertTrue( $this->info->getTimestamp() > 0 );
		$this->assertTrue( $this->info->getTitle() === 'test-title' );
		$this->assertTrue( $this->info->getType() === Tx_Extracache_Domain_Model_Info::TYPE_notice );
	}
}