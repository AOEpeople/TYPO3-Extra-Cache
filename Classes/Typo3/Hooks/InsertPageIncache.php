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
 * Hook to post-process entries in cache_pages to add the current group list.
 * 
 * @package extracache
 * @subpackage Typo3_Hooks
 */
class Tx_Extracache_Typo3_Hooks_InsertPageIncache {
	/**
	 * @var Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	private $typo3DbBackend;

	/**
	 * Modifies the group list column of a cache_pages entry.
	 *
	 * @param tslib_fe $parent
	 * @param integer $expires
	 * @return void
	 */
	public function insertPageIncache(tslib_fe $parent, $expires) {
		// @todo Add tag for group list if Caching Framework is used
		if ($parent->gr_list !== '0,-1') {
			$sqlFrom = 'cache_pages';
			$sqlWhere = 'hash=' . $this->getTypo3DbBackend()->fullQuoteStr($parent->newHash, 'cache_pages') . ' AND page_id=' . intval($parent->id);
			$modifiedValues = array('tx_extracache_grouplist' => $parent->gr_list); 
			$this->getTypo3DbBackend()->updateQuery($sqlFrom, $sqlWhere, $modifiedValues);
		}
	}

	/**
	 * @return Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	protected function getTypo3DbBackend() {
		if($this->typo3DbBackend === NULL) {
			$this->typo3DbBackend = t3lib_div::makeInstance('Tx_Extracache_System_Persistence_Typo3DbBackend');
		}
		return $this->typo3DbBackend;
	}
}