<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 AOE media GmbH <dev@aoemedia.de>
 *  All rights reserved
 *
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Abstract base class for extracache Database-Tests
 *
 * @package extracache
 * @subpackage Tests
 */
abstract class Tx_Extracache_Tests_AbstractDatabaseTestcase extends tx_phpunit_database_testcase {
	/**
	 * @var Tx_Extbase_Utility_ClassLoader
	 */
	private $classLoader;
	
	/**
	 * Initializes common extensions.
	 *
	 * @return void
	 */
	protected function initializeCommonExtensions() {
		if (t3lib_extMgm::isLoaded('aoe_dbsequenzer')) {
			$this->importExtensions(array('aoe_dbsequenzer'));
		}
	}
	/**
	 * @string
	 */
	protected function loadClass($className) {
		$this->getClassLoader()->loadClass( $className );
	}

	/**
	 * @return Tx_Extbase_Utility_ClassLoader
	 */
	private function getClassLoader() {
		if($this->classLoader === NULL) {
			$this->classLoader = new Tx_Extbase_Utility_ClassLoader();
		}
		return $this->classLoader;
	}
}