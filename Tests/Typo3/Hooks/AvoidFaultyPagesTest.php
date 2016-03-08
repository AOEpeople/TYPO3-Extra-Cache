<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2010 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Test case for tx_Extracache_Typo3_Hooks_AvoidFaultyPages
 *
 * @package extracache_tests
 * @subpackage Typo3_Hooks
 */
class Tx_Extracache_Typo3_Hooks_AvoidFaultyPagesTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var tx_Extracache_Typo3_Hooks_AvoidFaultyPages
	 */
	private $hook;
	/**
	 * @var TypoScriptFrontendController
	 */
	private $tsfeMock;

	/**
	 * Sets up this test case.
	 */
	protected function setUp() {
		$this->hook = new tx_Extracache_Typo3_Hooks_AvoidFaultyPages();
		$this->tsfeMock = $this->getMock('TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController', array(), array(), '', FALSE);
	}

	/**
	 * Cleans up this test case.
	 */
	protected function tearDown() {
		unset($this->tsfeMock);
		unset($this->hook);
	}

	/**
	 * Tests whether the no_cache variable is set.
	 *
	 * @test
	 */
	public function isNoCacheVariableSetOnFaultyPages() {
		$this->tsfeMock->no_cache = 0;

		$this->tsfeMock->content = '';
		$this->hook->handleFaultyEvent();
		$this->hook->disableCachingOnFaultyPages(array(), $this->tsfeMock);

		$this->assertTrue((bool) $this->tsfeMock->no_cache);
	}
	/**
	 * Tests whether the no_cache variable is set.
	 *
	 * @test
	 */
	public function isNoCacheVariableSetOnTemplaVoilaError() {
		$this->tsfeMock->no_cache = 0;

		$this->tsfeMock->content = 'BEGIN ' . tx_Extracache_Typo3_Hooks_AvoidFaultyPages::ERROR_TemplaVoila . ' END';
		$this->hook->disableCachingOnFaultyPages(array(), $this->tsfeMock);

		$this->assertTrue((bool) $this->tsfeMock->no_cache);
	}
}