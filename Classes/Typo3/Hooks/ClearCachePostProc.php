<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 AOE GmbH <dev@aoe.com>
*  All rights reserved
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Hook to delete some stuff, if cache should be cleared
 * 
 * Attention: this class-name must begin with 'tx' and NOT with 'Tx'...otherwise this hook will not work!
 * 
 * @package extracache
 * @subpackage Typo3_Hooks
 */
class tx_Extracache_Typo3_Hooks_ClearCachePostProc {
	/**
	 * @var Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	private $typo3DbBackend;

	/**
	 * delete eventqueue-table if cacheCmd is 'all' or 'pages'
	 *
	 * @param array $params
	 * @return void
	 */
	public function clearCachePostProc(array $params) {
		if (isset($params['cacheCmd']) && in_array($params['cacheCmd'], array('all', 'pages'))) {
			$this->getTypo3DbBackend()->deleteQuery('tx_extracache_eventqueue', '');
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