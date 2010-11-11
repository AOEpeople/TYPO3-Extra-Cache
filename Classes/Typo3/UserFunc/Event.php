<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 AOE media GmbH <dev@aoemedia.de>
*  All rights reserved
*
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * gets the cacheEvent-items, which are used inside the TYPO3-BE
 *
 * @package extracache
 * @subpackage Typo3_UserFunc
 *
 */
class Tx_Extracache_Typo3_UserFunc_Event {
	/**
	 * @var Tx_Extracache_Domain_Repository_EventRepository
	 */
	private $eventRepository;
	
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
		}
		array_multisort($tempItems['name'], SORT_ASC, $tempItems['key']);

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
		return t3lib_div::makeInstance('Tx_Extracache_Domain_Repository_EventRepository');
	}
}