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
 * Test case for tx_Extracache_Typo3_Hooks_IgnoreTypo3Cache
 *
 * @package extracache_tests
 * @subpackage Typo3_Hooks
 */
class Tx_Extracache_Typo3_Hooks_IgnoreTypo3CacheTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var boolean
	 */
	protected $backupGlobals = TRUE;
	/**
	 * @var tx_Extracache_Typo3_Hooks_IgnoreTypo3Cache
	 */
	private $ignoreTypo3Cache;
	/**
	 * @var TypoScriptFrontendController
	 */
	private $tsfe;

	/**
	 * Sets up this test case.
	 */
	protected function setUp() {
		$this->ignoreTypo3Cache = $this->getMock('tx_Extracache_Typo3_Hooks_IgnoreTypo3Cache', array('getBackendUser'));
		$this->tsfe = $this->getMock('TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController', array(), array(), '', FALSE);
	}
	/**
	 * Cleans this test case
	 */
	protected function tearDown() {
		unset($this->tsfe);
		unset($this->ignoreTypo3Cache);
	}

	/**
	 * Tests whether ignoring existing TYPO3 caches works if an accordant HTTP header is delivered.
	 *
	 * @test
	 */
	public function isExistingCacheIgnoredOnGivenHttpHeader() {
		$_SERVER['HTTP_' . str_replace('-', '_', tx_Extracache_Typo3_Hooks_StaticFileCache_DirtyPagesHook::HTTP_Request_Header)]= TRUE;
		$this->ignoreTypo3Cache->expects($this->once())->method('getBackendUser')->will($this->returnValue( NULL ));

		$disableAcquireCacheData = FALSE;
		$parameters = array('disableAcquireCacheData' => &$disableAcquireCacheData);

		$this->ignoreTypo3Cache->ignoreExistingCache ($parameters, $this->tsfe);
		$this->assertFalse((bool) $this->tsfe->no_cache);
		$this->assertTrue($disableAcquireCacheData);
	}

	/**
	 * Tests whether ignoring existing TYPO3 caches works if a backend user is active.
	 *
	 * @test
	 */
	public function isExistingCacheIgnoredOnActiveBackendUser() {
		$backendUser = $this->getMock('\TYPO3\CMS\Core\Authentication\BackendUserAuthentication');
		$backendUser->user = array('uid' => 1);
		$this->ignoreTypo3Cache->expects($this->once())->method('getBackendUser')->will($this->returnValue($backendUser));

		$disableAcquireCacheData = FALSE;
		$parameters = array('disableAcquireCacheData' => &$disableAcquireCacheData);

		$this->ignoreTypo3Cache->ignoreExistingCache ($parameters, $this->tsfe);
		$this->assertTrue((bool) $this->tsfe->no_cache);
		$this->assertTrue($disableAcquireCacheData);
	}
}