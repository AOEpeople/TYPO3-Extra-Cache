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

use TYPO3\CMS\Core\Utility\GeneralUtility;

require_once dirname ( __FILE__ ) . '/../../AbstractTestcase.php';

/**
 * test case for Tx_Extracache_Domain_Repository_ArgumentRepository
 * @package extracache_tests
 * @subpackage Domain_Repository
 */
class Tx_Extracache_Domain_Repository_ArgumentRepositoryTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 *
	 * @var Tx_Extracache_Domain_Repository_ArgumentRepository
	 */
	private $repository;
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->repository = new Tx_Extracache_Domain_Repository_ArgumentRepository();
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->repository );
	}

	/**
	 * test method addArgument
	 * @test
	 */
	public function addArgument() {
		$argument = GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_Argument', 'arg', Tx_Extracache_Domain_Model_Argument::TYPE_whitelist, true);
		$this->assertTrue ( count($this->repository->getArgumentsByType (Tx_Extracache_Domain_Model_Argument::TYPE_whitelist)) === 0 );
		$this->repository->addArgument($argument);
		$this->assertTrue ( count($this->repository->getArgumentsByType (Tx_Extracache_Domain_Model_Argument::TYPE_whitelist)) === 1 );
	}
	/**
	 * test method getArgumentsByType
	 * @test
	 */
	public function getArgumentsByType() {
		$argument1 = GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_Argument', 'arg1', Tx_Extracache_Domain_Model_Argument::TYPE_whitelist, true);
		$argument2 = GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_Argument', 'arg2', Tx_Extracache_Domain_Model_Argument::TYPE_unprocessible, '*');

		$this->assertTrue ( count($this->repository->getArgumentsByType (Tx_Extracache_Domain_Model_Argument::TYPE_unprocessible)) === 0 );
		$this->assertTrue ( count($this->repository->getArgumentsByType (Tx_Extracache_Domain_Model_Argument::TYPE_whitelist)) === 0 );

		$this->repository->addArgument($argument1);
		$this->assertTrue ( count($this->repository->getArgumentsByType (Tx_Extracache_Domain_Model_Argument::TYPE_unprocessible)) === 0 );
		$this->assertTrue ( count($this->repository->getArgumentsByType (Tx_Extracache_Domain_Model_Argument::TYPE_whitelist)) === 1 );

		$this->repository->addArgument($argument2);
		$this->assertTrue ( count($this->repository->getArgumentsByType (Tx_Extracache_Domain_Model_Argument::TYPE_unprocessible)) === 1 );
		$this->assertTrue ( count($this->repository->getArgumentsByType (Tx_Extracache_Domain_Model_Argument::TYPE_whitelist)) === 1 );
	}
}