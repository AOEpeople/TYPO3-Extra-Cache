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
 * test case for Tx_Extracache_Domain_Model_Argument
 * @package extracache_tests
 * @subpackage Domain_Model
 */
class Tx_Extracache_Domain_Model_ArgumentTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * 
	 * @var Tx_Extracache_Domain_Model_Argument
	 */
	private $argument;
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->argument = new Tx_Extracache_Domain_Model_Argument('argumentName', 'argumentType', 'argumentValue');
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->argument );
	}
	/**
	 * @test
	 */
	public function getFunctions() {
		$this->assertTrue( $this->argument->getName() === 'argumentName' );
		$this->assertTrue( $this->argument->getType() === 'argumentType' );
		$this->assertTrue( $this->argument->getValue() === 'argumentValue' );
	}
	/**
	 * @test
	 */
	public function getSupportedTypes() {
		$supportedTypes = Tx_Extracache_Domain_Model_Argument::getSupportedTypes();
		$this->assertTrue( count($supportedTypes) === 3 );
		$this->assertTrue( $supportedTypes[0] ===  Tx_Extracache_Domain_Model_Argument::TYPE_ignoreOnCreatingCache);
		$this->assertTrue( $supportedTypes[1] ===  Tx_Extracache_Domain_Model_Argument::TYPE_unprocessible);
		$this->assertTrue( $supportedTypes[2] ===  Tx_Extracache_Domain_Model_Argument::TYPE_whitelist);
	}
}