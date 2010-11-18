<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 AOE media GmbH <dev@aoemedia.de>
*  All rights reserved
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Hook class for TYPO3 - Sends HTTP headers for debuging caching situations (if developmentContext is set)
 * 
 * @package extracache
 * @subpackage Typo3_Hooks
 */
class Tx_Extracache_Typo3_Hooks_SendCacheDebugHeader {
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
	 * @param	tslib_fe		$parent The calling parent object
	 * @return	void
	 */
	public function sendCacheDebugHeader(tslib_fe $parent) {
		if ((boolean) $this->getExtensionManager()->get('developmentContext') === TRUE) {
			$cacheDebug = array();

			if ($parent->isINTincScript()) {
				$cacheDebug[] = 'INT';
			}
			if ($parent->isEXTincScript()) {
				$cacheDebug[] = 'EXT';
			}
			if ($parent->no_cache) {
				$cacheDebug[] = 'no_cache';
			}
			if ($parent->cacheContentFlag) {
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
			$this->extensionManager = t3lib_div::makeInstance('Tx_Extracache_Configuration_ExtensionManager');
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