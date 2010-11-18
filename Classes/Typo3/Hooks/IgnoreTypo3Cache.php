<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2010 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once(PATH_tx_extracache . 'Classes/Typo3/Hooks/StaticFileCache/DirtyPagesHook.php');

/**
 * Hook to ensure that there is no TYPO3 cached used.
 * The hook is called by the regular TYPO3 frontend object (TSFE).
 *
 * @package extracache
 * @subpackage Typo3_Hooks
 */
class Tx_Extracache_Typo3_Hooks_IgnoreTypo3Cache {
	/**
	 * @var t3lib_beUserAuth
	 */
	protected $backendUser;

	/**
	 * set the backend user as property 
	 */
	public function __construct() {
		if (isset($GLOBALS['BE_USER'])) {
			$this->setBackendUser($GLOBALS['BE_USER']);
		}
	}

	/**
	 * Ignores an existing TYPO3 cache to force recreation on processing dirty pages.
	 * This hook sets $TSFE->all to an empty string thus simulates a non-existing cache
	 * for the requested page.
	 *
	 * @param array $parameters
	 * @param tslib_fe $parent
	 * @return void
	 * @see tslib_fe::headerNoCache
	 * @see tslib_fe::getFromCache
	 */
	public function ignoreExistingCache(array $parameters, tslib_fe $parent) {
		if($this->isProcessingDirtyPages()) {
			$parent->all = '';
		}
		if ($this->isBackendUserActive()) {
			$parameters['disableAcquireCacheData'] = TRUE;
			$parent->no_cache = TRUE;
		}
	}

	/**
	 * @return t3lib_beUserAuth
	 */
	protected function getBackendUser() {
		return $this->backendUser;
	}
	/**
	 * @param t3lib_beUserAuth $backendUser
	 * @return void
	 */
	protected function setBackendUser(t3lib_beUserAuth $backendUser = NULL) {
		$this->backendUser = $backendUser;
	}

	/**
	 * Determines whether a backend user is active.
	 *
	 * @return boolean
	 */
	private function isBackendUserActive() {
		$backendUser = $this->getBackendUser();
		return (!is_null($backendUser) && isset($backendUser->user['uid']) && $backendUser->user['uid']);
	}
	/**
	 * Determines whether a dirty page is processing
	 * 
	 * @return boolean
	 */
	private function isProcessingDirtyPages() {
		$requestHeader = 'HTTP_' . str_replace('-', '_', Tx_Extracache_Typo3_Hooks_StaticFileCache_DirtyPagesHook::HTTP_Request_Header);
		return isset($_SERVER[$requestHeader]);
	}
}