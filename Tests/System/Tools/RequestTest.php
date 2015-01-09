<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2011 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once dirname ( __FILE__ ) . '/../../AbstractTestcase.php';

/**
 * test case for Tx_Extracache_System_Tools_Request
 * @package extracache_tests
 * @subpackage System_Tools
 */
class Tx_Extracache_System_Tools_RequestTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * Test method isUnprocessibleRequest
	 * @test
	 */
	public function isUnprocessibleRequest_isFalse() {
		$requestIsProcessibleTest1 = array();
		$requestIsProcessibleTest1['definedArguments'] = array( 'order' => 'value' );
		$requestIsProcessibleTest1['unprocessibleArguments'] = array('basket' => true);
		$requestIsProcessibleTest2 = array();
		$requestIsProcessibleTest2['definedArguments'] = array( 'action' => 'show' );
		$requestIsProcessibleTest2['unprocessibleArguments'] = array('action' => 'delete');
		$requestIsProcessibleTest3 = array();
		$requestIsProcessibleTest3['definedArguments'] = array( 'action' => 'show' );
		$requestIsProcessibleTest3['unprocessibleArguments'] = array('*' => array('action' => array('insert', 'update')));
		$requestIsProcessibleTest4 = array();
		$requestIsProcessibleTest4['definedArguments'] = array( 'basket' => array('action' => 'show') );
		$requestIsProcessibleTest4['unprocessibleArguments'] = array('*' => array('action' => array('insert', 'update')));
		$requestIsProcessibleTest5 = array();
		$requestIsProcessibleTest5['definedArguments'] = array( 'basket' => 'value' );
		$requestIsProcessibleTest5['unprocessibleArguments'] = array('basket' => array('action' => array('insert', 'update')));
		$requestIsProcessibleTest6 = array();
		$requestIsProcessibleTest6['definedArguments'] = array( 'basket' => array('action' => 'show') );
		$requestIsProcessibleTest6['unprocessibleArguments'] = array('basket' => array('action' => array('insert', 'update')));
		$requestIsProcessibleTests = array();
		$requestIsProcessibleTests[] = $requestIsProcessibleTest1;
		$requestIsProcessibleTests[] = $requestIsProcessibleTest2;
		$requestIsProcessibleTests[] = $requestIsProcessibleTest3;
		$requestIsProcessibleTests[] = $requestIsProcessibleTest4;
		$requestIsProcessibleTests[] = $requestIsProcessibleTest5;
		$requestIsProcessibleTests[] = $requestIsProcessibleTest6;

		foreach($requestIsProcessibleTests as $requestIsProcessibleTest) {
			$isUnprocessibleRequest = Tx_Extracache_System_Tools_Request::isUnprocessibleRequest( $requestIsProcessibleTest['definedArguments'], $this->createArgumentObjects($requestIsProcessibleTest['unprocessibleArguments']) );
			$this->assertFalse( $isUnprocessibleRequest );
		}
	}
	/**
	 * Test method isUnprocessibleRequest
	 * @test
	 */
	public function isUnprocessibleRequest_isTrue() {
		$requestIsProcessibleTest1 = array();
		$requestIsProcessibleTest1['definedArguments'] = array( 'basket' => 'delete' );
		$requestIsProcessibleTest1['unprocessibleArguments'] = array('basket' => true);
		$requestIsProcessibleTest2 = array();
		$requestIsProcessibleTest2['definedArguments'] = array( 'basket' => array('action' => 'delete') );
		$requestIsProcessibleTest2['unprocessibleArguments'] = array('basket' => true);
		$requestIsProcessibleTest3 = array();
		$requestIsProcessibleTest3['definedArguments'] = array( 'action' => 'delete' );
		$requestIsProcessibleTest3['unprocessibleArguments'] = array('action' => 'delete');
		$requestIsProcessibleTest4 = array();
		$requestIsProcessibleTest4['definedArguments'] = array( 'basket' => array('action' => 'insert') );
		$requestIsProcessibleTest4['unprocessibleArguments'] = array('*' => array('action' => array('insert', 'update')));
		$requestIsProcessibleTest5 = array();
		$requestIsProcessibleTest5['definedArguments'] = array( 'basket' => array('action' => 'insert') );
		$requestIsProcessibleTest5['unprocessibleArguments'] = array('basket' => array('action' => array('insert', 'update')));
		$requestIsProcessibleTests = array();
		$requestIsProcessibleTests[] = $requestIsProcessibleTest1;
		$requestIsProcessibleTests[] = $requestIsProcessibleTest2;
		$requestIsProcessibleTests[] = $requestIsProcessibleTest3;
		$requestIsProcessibleTests[] = $requestIsProcessibleTest4;
		$requestIsProcessibleTests[] = $requestIsProcessibleTest5;

		foreach($requestIsProcessibleTests as $requestIsProcessibleTest) {
			$isUnprocessibleRequest = Tx_Extracache_System_Tools_Request::isUnprocessibleRequest( $requestIsProcessibleTest['definedArguments'], $this->createArgumentObjects($requestIsProcessibleTest['unprocessibleArguments']) );
			$this->assertTrue( $isUnprocessibleRequest );
		}
	}

	/**
	 * @param	array $argumentsConfig
	 * @return	array
	 */
	private function createArgumentObjects(array $argumentsConfig) {
		$arguments = array();
		foreach($argumentsConfig as $name => $value) {
			$arguments[] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, Tx_Extracache_Domain_Model_Argument::TYPE_unprocessible, $value);
		}
		return $arguments;
	}
}