<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 AOE GmbH <dev@aoe.com>
*  All rights reserved
*
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * gets the cacheCleanerStrategy-items, which are used inside the TYPO3-BE
 *
 * @package extracache
 * @subpackage Typo3_UserFunc
 *
 */
class Tx_Extracache_Typo3_UserFunc_CleanerStrategy {
	/**
	 * rendering the TCA field (HTML-Code) for the backend of TYPO3. (the cacheCleanerStrategy)
	 * This field is meant to show a selection of the available cacheCleanerStrategies
	 *
	 * @param array &$parameters
	 * @return string	the HTML for the field
	 */
	public function getCleanerStrategyItemsProcFunc(array &$parameters) {
		// sort items
		$tempItems = array('key' => array(), 'name' => array());
		foreach($this->getCleanerStrategyRepository()->getAllStrategies() as $strategy) {
			$tempItems['key'][] = $strategy->getKey();
			$tempItems['name'][] = $strategy->getName();
			$tempItems['sortname'][] = strtolower($strategy->getName());
		}
		array_multisort($tempItems['sortname'], SORT_ASC, $tempItems['name'], $tempItems['key']);

		// add Items
		$items =& $parameters['items'];
		for($i=0;$i<count($tempItems['key']);$i++) {
			$items[] = array($tempItems['name'][$i],$tempItems['key'][$i]);
		}
	}
	/**
	 * @return Tx_Extracache_Domain_Repository_CleanerStrategyRepository
	 */
	protected function getCleanerStrategyRepository() {
		return GeneralUtility::makeInstance('Tx_Extracache_Domain_Repository_CleanerStrategyRepository');
	}
}