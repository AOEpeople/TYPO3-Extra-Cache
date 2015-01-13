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

use TYPO3\CMS\Core\Utility\GeneralUtility;

require_once dirname ( __FILE__ ) . '/../../../AbstractTestcase.php';

/**
 * test case for Tx_Extracache_System_Event_Events_EventOnStaticCacheResponsePostProcess
 * @package extracache_tests
 * @subpackage System_Event_Events
 */
class Tx_Extracache_System_Event_Events_EventOnStaticCacheResponsePostProcessTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var Tx_Extracache_System_Event_Events_EventOnStaticCacheResponsePostProcess
	 */
	private $event;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->event = GeneralUtility::makeInstance('Tx_Extracache_System_Event_Events_EventOnStaticCacheResponsePostProcess');
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
		$frontendUser = GeneralUtility::makeInstance ( 'tslib_feUserAuth' );
		$response = GeneralUtility::makeInstance('Tx_Extracache_System_StaticCache_Response');
		$this->assertTrue( $this->event->setFrontendUser( $frontendUser ) === $this->event );
		$this->assertTrue( $this->event->setResponse( $response ) === $this->event );
		$this->assertTrue( $this->event->getFrontendUser() === $frontendUser );
		$this->assertTrue( $this->event->getResponse() === $response );
	}
}