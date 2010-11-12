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
 * @package extracache
 */
class Tx_Extracache_Domain_Repository_EventRepository implements t3lib_Singleton {
	/**
	 * @var array
	 */
	private $events = array();

	/**
	 * @param Tx_Extracache_Domain_Model_Event $event
	 */
	public function addEvent(Tx_Extracache_Domain_Model_Event $event) {
		$this->events[] = $event;
	}
	/**
	 * @return array
	 */
	public function getEvents() {
		return $this->events;
	}
	/**
	 * @param	string $key
	 * @return	boolean
	 */
	public function hasEvent($key) {
		$hasEvent = false;
		foreach($this->getEvents() as $event) {
			if($event->getKey() === $key) {
				$hasEvent = true;
				break;
			}
		}
		return $hasEvent;
	}
}