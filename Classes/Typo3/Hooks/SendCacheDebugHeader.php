<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 AOE GmbH <dev@aoe.com>
*  All rights reserved
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Hook class for TYPO3 - Sends HTTP headers for debuging caching situations (if developmentContext is set)
 *
 * Attention: this class-name must begin with 'tx' and NOT with 'Tx'...otherwise this hook will not work!
 *
 * @package extracache
 * @subpackage Typo3_Hooks
 */
class tx_Extracache_Typo3_Hooks_SendCacheDebugHeader {
	const HTTP_Request_Header = 'X-TYPO3Cache';

	/**
	 * @var Tx_Extracache_Configuration_ExtensionManager
	 */
	private $extensionManager;

	/**
	 * Sends HTTP headers for debuging caching situations. The X-TYPO3Cache
	 * header indicated whether *_INT, *_EXT objects are used or caching got
	 * disabled during rendering by setting TSFE->set_no_cache().
	 *
	 * @param	array		$parameters Parameters delivered by the calling parent object (not used here)
	 * @param	TypoScriptFrontendController	$frontend The calling parent object
	 * @return	void
	 */
	public function sendCacheDebugHeader(array $parameters, TypoScriptFrontendController $frontend) {
		if ($this->getExtensionManager()->isDevelopmentContextSet()) {
			$cacheDebug = array();

			if ($frontend->isINTincScript()) {
				$cacheDebug[] = 'INT';
			}
			if ($frontend->no_cache) {
				$cacheDebug[] = 'no_cache';
			}
			if ($frontend->cacheContentFlag) {
				$cacheDebug[] = 'cached';
			}
			if (count($cacheDebug)) {
				$this->sendHttpRequestHeader($cacheDebug);
			}
		}
	}

	/**
	 * @return Tx_Extracache_Configuration_ExtensionManager
	 */
	protected function getExtensionManager() {
		if($this->extensionManager === NULL) {
			$this->extensionManager = GeneralUtility::makeInstance('Tx_Extracache_Configuration_ExtensionManager');
		}
		return $this->extensionManager;
	}
	/**
	 * send HTTP-Request-Header
	 *
	 * @param array $cacheDebug
	 */
	protected function sendHttpRequestHeader(array $cacheDebug) {
		header(self::HTTP_Request_Header . ': ' . implode(',', $cacheDebug));
	}
}
