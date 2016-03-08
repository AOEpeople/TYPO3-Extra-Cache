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
 * Hook to post-process entries in cache_pages to add the current group list.
 * This is needed if we want to delete the TYPO3-cache for a certain fe-group-list
 * 
 * Attention: this class-name must begin with 'tx' and NOT with 'Tx'...otherwise this hook will not work!
 * 
 * @package extracache
 * @subpackage Typo3_Hooks
 */
class tx_Extracache_Typo3_Hooks_InsertPageIncache {
	/**
	 * @var Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	private $typo3DbBackend;

	/**
	 * Modifies the group list column of a cache_pages entry.
	 *
	 * @param TypoScriptFrontendController $frontend
	 * @param integer $expires
	 * @return void
	 */
	public function insertPageIncache(TypoScriptFrontendController $frontend, $expires) {
		// @todo Add tag for group list if Caching Framework is used
		if ($frontend->gr_list !== '0,-1') {
			$sqlFrom = 'cache_pages';
			$sqlWhere = 'hash=' . $this->getTypo3DbBackend()->fullQuoteStr($frontend->newHash, 'cache_pages') . ' AND page_id=' . intval($frontend->id);
			$modifiedValues = array('tx_extracache_grouplist' => $frontend->gr_list);
			$this->getTypo3DbBackend()->updateQuery($sqlFrom, $sqlWhere, $modifiedValues);
		}
	}

	/**
	 * @return Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	protected function getTypo3DbBackend() {
		if($this->typo3DbBackend === NULL) {
			$this->typo3DbBackend = GeneralUtility::makeInstance('Tx_Extracache_System_Persistence_Typo3DbBackend');
		}
		return $this->typo3DbBackend;
	}
}
