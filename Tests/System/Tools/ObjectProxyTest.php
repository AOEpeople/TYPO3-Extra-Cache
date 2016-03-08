<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2010 AOE GmbH <dev@aoe.com>
 * All rights reserved
 *
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * test case for Tx_Extracache_System_Tools_ObjectProxy
 * @package extracache_tests
 * @subpackage System_Tools
 */
class Tx_Extracache_System_Tools_ObjectProxyTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var	Tx_Extracache_System_Tools_ObjectProxy
	 */
	private $objectProxy;
	/**
	 * @var	Tx_Extracache_System_Tools_Fixtures_DummyObject
	 */
	private $dummyObject;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$className = 'Tx_Extracache_System_Tools_Fixtures_DummyObject';
		$parentCallback = 'setDummyObject';
		$this->objectProxy = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_Extracache_System_Tools_ObjectProxy', $this, $className, $parentCallback);
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset($this->objectProxy);
	}

	/**
	 * Test Methods '__call', '__get' and '__set'
	 * @test
	 */
	public function callObjectProxy() {
		$this->assertEquals( $this->objectProxy->calculate(1,2,4), 7 );
		$this->assertInstanceOf( 'Tx_Extracache_System_Tools_Fixtures_DummyObject', $this->dummyObject );

		$this->objectProxy->setName('testName1');
		$this->assertEquals( $this->objectProxy->getName(), 'testName1' );
		
		$this->objectProxy->name = 'testName2';
		$this->assertEquals( $this->objectProxy->name, 'testName2' );
	}	
	/**
	 * @param Tx_Extracache_System_Tools_Fixtures_DummyObject $dummyObject
	 */
	public function setDummyObject(Tx_Extracache_System_Tools_Fixtures_DummyObject $dummyObject) {
		$this->dummyObject = $dummyObject;
	}
}