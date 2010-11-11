<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2009 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once dirname ( __FILE__ ) . '/../../AbstractTestcase.php';

/**
 * test case for Tx_Extracache_Validation_Validator_Argument
 * @package extracache
 * @subpackage Tests_Validation_Validator
 */
class Tx_Extracache_Tests_Validation_Validator_ArgumentTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * 
	 * @var Tx_Extracache_Validation_Validator_Argument
	 */
	private $validator;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->validator = new Tx_Extracache_Validation_Validator_Argument();
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->validator );
	}

	/**
	 * test method isValid
	 * @test
	 */
	public function nameIsValid() {
		$type = Tx_Extracache_Domain_Model_Argument::TYPE_whitelist;
		$value = true;

		$name = 'testname';
		$argument = t3lib_div::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, $type, $value);
		$this->assertTrue( $this->validator->isValid($argument) );

		$name = '*';
		$argument = t3lib_div::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, $type, $value);
		$this->assertTrue( $this->validator->isValid($argument) );
	}
	/**
	 * test method isValid
	 * @test
	 */
	public function nameIsNotValid() {
		$type = Tx_Extracache_Domain_Model_Argument::TYPE_whitelist;
		$value = true;

		$name = '';
		$argument = t3lib_div::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, $type, $value);
		$this->assertFalse( $this->validator->isValid($argument) );

		$name = '[testname]';
		$argument = t3lib_div::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, $type, $value);
		$this->assertFalse( $this->validator->isValid($argument) );
	}
	/**
	 * test method isValid
	 * @test
	 */
	public function typeIsValid() {
		$name = 'testname';
		$value = true;

		$type = Tx_Extracache_Domain_Model_Argument::TYPE_ignoreOnCreatingCache;
		$argument = t3lib_div::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, $type, $value);
		$this->assertTrue( $this->validator->isValid($argument) );

		$type = Tx_Extracache_Domain_Model_Argument::TYPE_unprocessible;
		$argument = t3lib_div::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, $type, $value);
		$this->assertTrue( $this->validator->isValid($argument) );

		$type = Tx_Extracache_Domain_Model_Argument::TYPE_whitelist;
		$argument = t3lib_div::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, $type, $value);
		$this->assertTrue( $this->validator->isValid($argument) );
	}
	/**
	 * test method isValid
	 * @test
	 */
	public function typeIsNotValid() {
		$name = 'testname';
		$value = true;

		$type = 'unknownTestType';
		$argument = t3lib_div::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, $type, $value);
		$this->assertFalse( $this->validator->isValid($argument) );
	}
	/**
	 * test method isValid
	 * @test
	 */
	public function valueIsValid() {
		$type = Tx_Extracache_Domain_Model_Argument::TYPE_whitelist;
		$name = 'testname';

		$value = true;
		$argument = t3lib_div::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, $type, $value);
		$this->assertTrue( $this->validator->isValid($argument) );

		$value = array('action' => 'show');
		$argument = t3lib_div::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, $type, $value);
		$this->assertTrue( $this->validator->isValid($argument) );

		$value = 'show';
		$argument = t3lib_div::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, $type, $value);
		$this->assertTrue( $this->validator->isValid($argument) );
	}
	/**
	 * test method isValid
	 * @test
	 */
	public function valueIsNotValid() {
		$type = Tx_Extracache_Domain_Model_Argument::TYPE_whitelist;
		$name = 'testname';

		$value = false;
		$argument = t3lib_div::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, $type, $value);
		$this->assertFalse( $this->validator->isValid($argument) );

		$value = array();
		$argument = t3lib_div::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, $type, $value);
		$this->assertFalse( $this->validator->isValid($argument) );
	}
}