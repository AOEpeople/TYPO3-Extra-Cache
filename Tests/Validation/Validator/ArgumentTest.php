<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2009 AOE GmbH <dev@aoe.com>
 * All rights reserved
 *
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * test case for Tx_Extracache_Validation_Validator_Argument
 * @package extracache_tests
 * @subpackage Validation_Validator
 */
class Tx_Extracache_Validation_Validator_ArgumentTest extends Tx_Extracache_Tests_AbstractTestcase {
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
		$argument = GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, $type, $value);
        $this->assertFalse($this->validator->validate($argument)->hasErrors());

		$name = '*';
		$argument = GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, $type, $value);
        $this->assertFalse($this->validator->validate($argument)->hasErrors());
	}
	/**
	 * test method isValid
	 * @test
	 */
	public function nameIsNotValid() {
		$type = Tx_Extracache_Domain_Model_Argument::TYPE_whitelist;
		$value = true;

		$name = '';
		$argument = GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, $type, $value);
        $this->assertTrue($this->validator->validate($argument)->hasErrors());

		$name = '[testname]';
		$argument = GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, $type, $value);
        $this->assertTrue($this->validator->validate($argument)->hasErrors());
	}
	/**
	 * test method isValid
	 * @test
	 */
	public function typeIsValid() {
		$name = 'testname';
		$value = true;

		$type = Tx_Extracache_Domain_Model_Argument::TYPE_ignoreOnCreatingCache;
		$argument = GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, $type, $value);
        $this->assertFalse($this->validator->validate($argument)->hasErrors());

		$type = Tx_Extracache_Domain_Model_Argument::TYPE_unprocessible;
		$argument = GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, $type, $value);
        $this->assertFalse($this->validator->validate($argument)->hasErrors());

		$type = Tx_Extracache_Domain_Model_Argument::TYPE_whitelist;
		$argument = GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, $type, $value);
        $this->assertFalse($this->validator->validate($argument)->hasErrors());
	}
	/**
	 * test method isValid
	 * @test
	 */
	public function typeIsNotValid() {
		$name = 'testname';
		$value = true;

		$type = 'unknownTestType';
		$argument = GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, $type, $value);
        $this->assertTrue($this->validator->validate($argument)->hasErrors());
	}
	/**
	 * test method isValid
	 * @test
	 */
	public function valueIsValid() {
		$type = Tx_Extracache_Domain_Model_Argument::TYPE_whitelist;
		$name = 'testname';

		$value = true;
		$argument = GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, $type, $value);
        $this->assertFalse($this->validator->validate($argument)->hasErrors());

		$value = array('action' => 'show');
		$argument = GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, $type, $value);
        $this->assertFalse($this->validator->validate($argument)->hasErrors());

		$value = 'show';
		$argument = GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, $type, $value);
        $this->assertFalse($this->validator->validate($argument)->hasErrors());
	}
	/**
	 * test method isValid
	 * @test
	 */
	public function valueIsNotValid() {
		$type = Tx_Extracache_Domain_Model_Argument::TYPE_whitelist;
		$name = 'testname';

		$value = false;
		$argument = GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, $type, $value);
        $this->assertTrue($this->validator->validate($argument)->hasErrors());

		$value = array();
		$argument = GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, $type, $value);
        $this->assertTrue($this->validator->validate($argument)->hasErrors());
	}
}