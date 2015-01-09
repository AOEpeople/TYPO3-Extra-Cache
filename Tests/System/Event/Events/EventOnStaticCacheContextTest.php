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
 * test case for Tx_Extracache_System_Event_Events_EventOnStaticCacheContext
 * @package extracache_tests
 * @subpackage System_Event_Events
 */
class Tx_Extracache_System_Event_Events_EventOnStaticCacheContextTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var Tx_Extracache_System_Event_Events_EventOnStaticCacheContext
	 */
	private $event;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->event = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_Extracache_System_Event_Events_EventOnStaticCacheContext');
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
		$this->assertTrue( $this->event->setStaticCacheContext( TRUE ) === $this->event );		
		$this->assertTrue( $this->event->getStaticCacheContext() === TRUE );
		$this->assertTrue( $this->event->setStaticCacheContext( FALSE ) === $this->event );
		$this->assertTrue( $this->event->getStaticCacheContext() === FALSE );
	}
}