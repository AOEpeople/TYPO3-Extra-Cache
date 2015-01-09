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

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * gets the cacheEvent-items, which are used inside the TYPO3-BE
 *
 * @package extracache
 * @subpackage Typo3_UserFunc
 *
 */
class Tx_Extracache_Typo3_UserFunc_Event {
	/**
	 * rendering the TCA field (HTML-Code) for the backend of TYPO3. (the cacheEvent)
	 * This field is meant to show a selection of the available cacheEvents
	 *
	 * @param array &$parameters
	 * @return string	the HTML for the field
	 */
	public function getEventItemsProcFunc(array &$parameters) {
		// sort items
		$tempItems = array('key' => array(), 'name' => array());
		foreach($this->getEventRepository()->getEvents() as $event) {
			$tempItems['key'][] = $event->getKey();
			$tempItems['name'][] = $event->getName();
			$tempItems['sortname'][] = strtolower($event->getName());
		}
		array_multisort($tempItems['sortname'], SORT_ASC, $tempItems['name'], $tempItems['key']);

		// add Items
		$items =& $parameters['items'];
		for($i=0;$i<count($tempItems['key']);$i++) {
			$items[] = array($tempItems['name'][$i],$tempItems['key'][$i]);
		}
	}
	
	/**
	 * @return Tx_Extracache_Domain_Repository_EventRepository
	 */
	protected function getEventRepository() {
		return GeneralUtility::makeInstance('Tx_Extracache_Domain_Repository_EventRepository');
	}
}