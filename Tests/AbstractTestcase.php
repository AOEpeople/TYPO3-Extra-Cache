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

require_once dirname(__FILE__) . '/Exception.php';

/**
 * Abstract base class for extracache Tests
 *
 * @package extracache
 * @subpackage Tests
 */
abstract class Tx_Extracache_Tests_AbstractTestcase extends tx_phpunit_testcase {
	/**
	 * @var Tx_Extbase_Utility_ClassLoader
	 */
	private $classLoader;
	
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

	/**
	 * Throws an exception to be used in the test cases only.
	 *
	 * @throws Tx_Extracache_Tests_Exception
	 * @param string $message
	 * @return void
	 */
	public function throwExceptionCallback() {
		throw new Tx_Extracache_Tests_Exception('Test Exception');
	}
}