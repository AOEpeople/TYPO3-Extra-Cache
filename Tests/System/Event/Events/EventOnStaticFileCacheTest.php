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
 * test case for Tx_Extracache_System_Event_Events_EventOnStaticFileCache
 * @package extracache_tests
 * @subpackage System_Event_Events
 */
class Tx_Extracache_System_Event_Events_EventOnStaticFileCacheTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var Tx_Extracache_System_Event_Events_EventOnStaticFileCache
	 */
	private $event;
	/**
	 * @var tx_ncstaticfilecache
	 */
	private $mockedStaticFileCache;
	/**
	 * @var tslib_fe
	 */
	private $mockedFrontend;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->mockedStaticFileCache = $this->getMock('tx_ncstaticfilecache',array(),array(),'',FALSE);
		$this->mockedFrontend = $this->getMock('tslib_fe',array(),array(),'',FALSE);
		$this->event = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_Extracache_System_Event_Events_EventOnStaticFileCache', 'testEventName', $this, array(), $this->mockedStaticFileCache, $this->mockedFrontend);
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
		$this->assertEquals( $this->event->getFrontend(), $this->mockedFrontend );
		$this->assertEquals( $this->event->getName(), 'testEventName' );
		$this->assertEquals( $this->event->getParent(), $this->mockedStaticFileCache );
	}
}