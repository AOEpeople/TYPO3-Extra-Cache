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
 * Hook class for TYPO3 - is called before putting data into caches.
 * This hook avoids putting faulty pages (e.g. if templaVoila could not render page) into the cache.
 * 
 * Attention: this class-name must begin with 'tx' and NOT with 'Tx'...otherwise this hook will not work!
 * 
 * @package extracache
 * @subpackage Typo3_Hooks
 */
class tx_Extracache_Typo3_Hooks_PostProcessContentHook {
	/**
	 * @var tx_Extracache_Typo3_TypoScriptCache
	 */
	protected $typoScriptCache;

	/**
	 * Adds the TypoScript template page id to the cached config array of TSFE.
	 *
	 * @param array $parameters
	 * @param tslib_fe $parent
	 * @return void
	 */
	public function addTemplatePageId(array $parameters, tslib_fe $parent) {
		$parent->config[tx_Extracache_Typo3_TypoScriptCache::CONFIG_Key] = $this->getTypoScriptCache()->getTemplatePageId($parent);
	}

	/**
	 * Gets an instance of the TypoScript cache.
	 *
	 * @return tx_Extracache_Typo3_TypoScriptCache
	 */
	protected function getTypoScriptCache() {
		if($this->typoScriptCache === NULL) {
			$this->typoScriptCache = t3lib_div::makeInstance('tx_Extracache_Typo3_TypoScriptCache');
		}
		return $this->typoScriptCache;
	}
}