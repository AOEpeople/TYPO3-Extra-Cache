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
 * This hook avoids putting faulty pages (e.g. if templaVoila could not render page or Event 'Tx_Extracache_System_Event_Events_EventOnFaultyPages' was thrown) into the cache.
 * 
 * Attention: this class-name must begin with 'tx' and NOT with 'Tx'...otherwise this hook will not work!
 * 
 * @package extracache
 * @subpackage Typo3_Hooks
 */
class tx_Extracache_Typo3_Hooks_AvoidFaultyPages implements t3lib_Singleton {
	const ERROR_TemplaVoila = '<!-- TemplaVoila ERROR message: -->';

	/**
	 * @var boolean
	 */
	protected $hasFaultyEvents = FALSE;

	/**
	 * Disables the caching on faulty pages
	 *
	 * @param	array		$parameters Additional parameters delivered by the calling parent
	 * @param	tslib_fe	$parent The calling parent object (TSFE)
	 * @return	void
	 */
	public function disableCachingOnFaultyPages(array $parameters, tslib_fe $parent) {
		if ($this->hasFaultyEvents || $this->hasTemplaVoilaError($parent)) {
			$parent->no_cache = 1;
		}
	}
	/**
	 * Handles event which produce faulty pages and disables caching in these situations.
	 *
	 * @return	void
	 */
	public function handleFaultyEvent() {
		$this->hasFaultyEvents = TRUE;
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