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
 * test case for Tx_Extracache_Domain_Model_ContentProcessorDefinition
 * @package extracache_tests
 * @subpackage Domain_Model
 */
class Tx_Extracache_Domain_Model_ContentProcessorDefinitionTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * 
	 * @var Tx_Extracache_Domain_Model_ContentProcessorDefinition
	 */
	private $contentProcessorDefinition;
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->contentProcessorDefinition = new Tx_Extracache_Domain_Model_ContentProcessorDefinition('dummyClassName', '/dummy/path/');
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->contentProcessorDefinition );
	}
	/**
	 * @test
	 */
	public function getFunctions() {
		$this->assertTrue( $this->contentProcessorDefinition->getClassName() === 'dummyClassName' );
		$this->assertTrue( $this->contentProcessorDefinition->getPath() === '/dummy/path/' );
	}
}