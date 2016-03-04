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

use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Hook to ensure that there is no TYPO3 cached used.
 * The hook is called by the regular TYPO3 frontend object (TSFE).
 * 
 * Attention: this class-name must begin with 'tx' and NOT with 'Tx'...otherwise this hook will not work!
 *
 * @package extracache
 * @subpackage Typo3_Hooks
 */
class tx_Extracache_Typo3_Hooks_IgnoreTypo3Cache {
	/**
	 * @var \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
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
	 * @param TypoScriptFrontendController $parent
	 * @return void
	 * @see TypoScriptFrontendController::headerNoCache
	 * @see TypoScriptFrontendController::getFromCache
	 */
	public function ignoreExistingCache(array &$parameters, TypoScriptFrontendController $parent) {
		// This modification triggers invalidating the TYPO3 Cache and recaching:
		if($this->isProcessingDirtyPages()) {
			// Disables a look-up for cached page data - thus resulting in re-generation of the page even if cached.
			$parameters['disableAcquireCacheData'] = TRUE;
		}

			// This modification just disables reading from and writing to the cache:
		if ($this->isBackendUserActive()) {
			$parameters['disableAcquireCacheData'] = TRUE;
			$parent->no_cache = TRUE;
		}
	}

	/**
	 * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
	 */
	protected function getBackendUser() {
		return $this->backendUser;
	}
	/**
	 * @param \TYPO3\CMS\Core\Authentication\BackendUserAuthentication $backendUser
	 * @return void
	 */
	protected function setBackendUser(\TYPO3\CMS\Core\Authentication\BackendUserAuthentication $backendUser = NULL) {
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
		$requestHeader = 'HTTP_' . str_replace('-', '_', tx_Extracache_Typo3_Hooks_StaticFileCache_DirtyPagesHook::HTTP_Request_Header);
		return isset($_SERVER[$requestHeader]);
	}
}