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
 * Test case for Tx_Extracache_Typo3_Hooks_IgnoreTypo3Cache
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
	 * @var string
	 */
	private $cacheData;
	/**
	 * @var Tx_Extracache_Typo3_Hooks_IgnoreTypo3Cache
	 */
	private $ignoreTypo3Cache;
	/**
	 * @var tslib_fe
	 */
	private $tsfe;

	/**
	 * Sets up this test case.
	 */
	protected function setUp() {
		$this->cacheData = uniqid('cache');
		$this->ignoreTypo3Cache = $this->getMock('Tx_Extracache_Typo3_Hooks_IgnoreTypo3Cache', array('getBackendUser'));
		$this->tsfe = $this->getMock('tslib_fe', array(), array(), '', FALSE);
		$this->tsfe->all = $this->cacheData;
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
		$_SERVER['HTTP_' . str_replace('-', '_', Tx_Extracache_Typo3_Hooks_StaticFileCache_DirtyPagesHook::HTTP_Request_Header)]= TRUE;
		$this->ignoreTypo3Cache->expects($this->once())->method('getBackendUser')->will($this->returnValue( NULL ));

		$this->ignoreTypo3Cache->ignoreExistingCache (array(), $this->tsfe);
		$this->assertEquals('', $this->tsfe->all);
	}

	/**
	 * Tests whether ignoring existing TYPO3 caches works if a backend user is active.
	 *
	 * @test
	 */
	public function isExistingCacheIgnoredOnActiveBackendUser() {
		$backendUser = $this->getMock('t3lib_beUserAuth');
		$backendUser->user = array('uid' => 1);
		$this->ignoreTypo3Cache->expects($this->once())->method('getBackendUser')->will($this->returnValue($backendUser));

		$disableAcquireCacheData = FALSE;
		$parameters = array('disableAcquireCacheData' => &$disableAcquireCacheData);

		$this->ignoreTypo3Cache->ignoreExistingCache ($parameters, $this->tsfe);
		$this->assertEquals($this->cacheData, $this->tsfe->all);
		$this->assertTrue((bool) $this->tsfe->no_cache);
		$this->assertTrue($disableAcquireCacheData);
	}
}