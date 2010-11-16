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

require_once dirname ( __FILE__ ) . '/../../../AbstractTestcase.php';

/**
 * test case for Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest
 * @package extracache_tests
 * @subpackage System_Event_Events
 */
class Tx_Extracache_System_Event_Events_EventOnStaticCacheRequestTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest
	 */
	private $event;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->event = t3lib_div::makeInstance('Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest');
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->event );
	}

	/**
	 * Test get-methods
	 * @test
	 */
	public function getMethods() {
		$request = t3lib_div::makeInstance('Tx_Extracache_System_StaticCache_Request');
		$frontendUser = t3lib_div::makeInstance ( 'tslib_feUserAuth' );
		$this->assertTrue( $this->event->setFrontendUser( $frontendUser ) === $this->event );
		$this->assertTrue( $this->event->setRequest( $request ) === $this->event );
		$this->assertTrue( $this->event->getFrontendUser() === $frontendUser );
		$this->assertTrue( $this->event->getRequest() === $request );
	}
}