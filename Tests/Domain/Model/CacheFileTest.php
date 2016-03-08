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
 * test case for Tx_Extracache_Domain_Model_CacheFile
 * @package extracache_tests
 * @subpackage Domain_Model
 */
class Tx_Extracache_Domain_Model_CacheFileTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * 
	 * @var Tx_Extracache_Domain_Model_CacheFile
	 */
	private $cacheFile;
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->cacheFile = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_CacheFile');
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->cacheFile );
	}
	/**
	 * @test
	 */
	public function getFunctions() {
		$this->cacheFile->setName('testName');
		$this->assertTrue( $this->cacheFile->getName() === 'testName' );
		$this->assertTrue( $this->cacheFile->getIdentifier() === base64_encode('testName') );
	}
}