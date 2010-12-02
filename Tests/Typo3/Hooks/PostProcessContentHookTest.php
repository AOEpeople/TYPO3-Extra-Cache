<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2010 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once dirname ( __FILE__ ) . '/../../AbstractTestcase.php';

/**
 * Test case for tx_Extracache_Typo3_Hooks_PostProcessContentHook
 *
 * @package extracache_tests
 * @subpackage Typo3_Hooks
 */
class Tx_Extracache_Typo3_Hooks_PostProcessContentHookTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var tx_Extracache_Typo3_Hooks_PostProcessContentHook
	 */
	private $hook;
	/**
	 * @var tslib_fe
	 */
	private $tsfeMock;

	/**
	 * @var tx_Extracache_Typo3_TypoScriptCache
	 */
	private $typoScriptCache;

	/**
	 * Sets up this test case.
	 */
	protected function setUp() {
		$this->typoScriptCache = $this->getMock('tx_Extracache_Typo3_TypoScriptCache', array('getTemplatePageId'));

		$this->hook = $this->getMock('tx_Extracache_Typo3_Hooks_PostProcessContentHook', array('getTypoScriptCache'));
		$this->hook->expects($this->any())->method('getTypoScriptCache')->will($this->returnValue($this->typoScriptCache));

		$this->tsfeMock = $this->getMock('tslib_fe', array(), array(), '', FALSE);
	}

	/**
	 * Cleans up this test case.
	 */
	protected function tearDown() {
		unset($this->typoScriptCache);
		unset($this->tsfeMock);
		unset($this->hook);
	}

	/**
	 * Tests whether the no_cache variable is set.
	 *
	 * @test
	 */
	public function isNoCacheVariableSetOnTemplaVoilaError() {
		$this->tsfeMock->no_cache = 0;

		$this->tsfeMock->content = 'BEGIN ' . tx_Extracache_Typo3_Hooks_PostProcessContentHook::ERROR_TemplaVoila . ' END';
		$this->hook->disableCachingOnFaultyPages(array(), $this->tsfeMock);

		$this->assertTrue((bool) $this->tsfeMock->no_cache);
	}

	/**
	 * Tests whether the TypoScript template page id is set in TSFE.
	 *
	 * @return void
	 * @test
	 */
	public function isTemplatePageIdSet() {
		$this->typoScriptCache->expects($this->once())->method('getTemplatePageId')->will($this->returnValue('987'));
		$this->tsfeMock->config = array();

		$this->hook->addTemplatePageId(array(), $this->tsfeMock);

		$this->assertEquals(987, $this->tsfeMock->config[tx_Extracache_Typo3_TypoScriptCache::CONFIG_Key]);
	}
}