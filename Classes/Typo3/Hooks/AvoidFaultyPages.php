<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 AOE media GmbH <dev@aoemedia.de>
*  All rights reserved
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * This hook avoids putting faulty pages (e.g. if templaVoila could not render page or Event 'Tx_Extracache_System_Event_Events_EventOnFaultyPages' was thrown) into the cache.
 * 
 * Attention: this class-name must begin with 'tx' and NOT with 'Tx'...otherwise this hook will not work!
 * 
 * @package extracache
 * @subpackage Typo3_Hooks
 */
class tx_Extracache_Typo3_Hooks_AvoidFaultyPages implements \TYPO3\CMS\Core\SingletonInterface {
	const ERROR_TemplaVoila = '<!-- TemplaVoila ERROR message: -->';

	/**
	 * @var boolean
	 */
	protected $hasFaultyEvents = FALSE;

	/**
	 * Disables the caching on faulty pages
	 *
	 * @param	array		$parameters Additional parameters delivered by the calling parent
	 * @param	TypoScriptFrontendController	$frontend The calling parent object (TSFE)
	 * @return	void
	 */
	public function disableCachingOnFaultyPages(array $parameters, TypoScriptFrontendController $frontend) {
		if ($this->hasFaultyEvents || $this->hasTemplaVoilaError($frontend)) {
			$frontend->no_cache = 1;
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
	 * @param TypoScriptFrontendController $frontend
	 * @return boolean
	 */
	private function hasTemplaVoilaError(TypoScriptFrontendController $frontend) {
		return (stripos($frontend->content, self::ERROR_TemplaVoila) !== FALSE);
	}
}
