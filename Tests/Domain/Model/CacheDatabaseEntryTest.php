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
 * test case for Tx_Extracache_Domain_Model_CacheDatabaseEntry
 * @package extracache_tests
 * @subpackage Domain_Model
 */
class Tx_Extracache_Domain_Model_CacheDatabaseEntryTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * 
	 * @var Tx_Extracache_Domain_Model_CacheDatabaseEntry
	 */
	private $dbEntry;
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->dbEntry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_CacheDatabaseEntry');
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->dbEntry );
	}
	/**
	 * @test
	 */
	public function getFunctions() {
		$this->dbEntry->setRecordKeys( array('key1','key2'));
		$this->dbEntry->setKey1('name1');
		$this->dbEntry->setKey2('name2');
		$this->assertTrue( $this->dbEntry->getRecordKeys() === array('key1','key2') );
		$this->assertTrue( $this->dbEntry->getKey1() === 'name1' );
		$this->assertTrue( $this->dbEntry->getKey2() === 'name2' );
	}
}