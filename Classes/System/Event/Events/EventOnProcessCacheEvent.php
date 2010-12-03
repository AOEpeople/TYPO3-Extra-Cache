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
 * This event will be thrown if cacheEvent should be processed.
 * This event must be thrown by external extensions, which wants to use the feature 'cache-events'.
 * 
 * @package extracache
 * @subpackage System_Event_Events
 */
class Tx_Extracache_System_Event_Events_EventOnProcessCacheEvent extends Tx_Extracache_System_Event_Events_Event {
	/**
	 * name of the event
	 * @var string
	 */
	protected $name = 'onProcessCacheEvent';
	/**
	 * name of cacheEvent
	 * @var string
	 */
	private $cacheEvent;
	
	/**
	 * Constructs this event.
	 *
	 * @param string $cacheEvent
	 */
	public function __construct($cacheEvent) {
		$this->setCacheEvent($cacheEvent);
	}
	/**
	 * @return string
	 */
	public function getCacheEvent() {
		return $this->cacheEvent;
	}

	/**
	 * @param string $cacheEvent
	 */
	private function setCacheEvent($cacheEvent) {
		$this->cacheEvent = $cacheEvent;
	}
}