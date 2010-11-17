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
 * @package extracache
 * @subpackage Typo3_Hooks
 */
class Tx_Extracache_Typo3_Hooks_AvoidFaultyPagesHook {
	const ERROR_TemplaVoila = '<!-- TemplaVoila ERROR message: -->';

	/**
	 * Disables the caching on faulty pages
	 *
	 * @param	array		$parameters Additional parameters delivered by the calling parent
	 * @param	tslib_fe	$parent The calling parent object (TSFE)
	 * @return	void
	 */
	public function disableCachingOnFaultyPages(array $parameters, tslib_fe $parent) {
		if ($this->hasTemplaVoilaError($parent)) {
			$parent->no_cache = 1;
		}
	}

	/**
	 * Determines whether a TemplaVoila error was found in the content.
	 *
	 * @param tslib_fe $tsfe
	 * @return boolean
	 */
	private function hasTemplaVoilaError(tslib_fe $tsfe) {
		return (stripos($tsfe->content, self::ERROR_TemplaVoila) !== FALSE);
	}
}