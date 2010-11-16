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
 * this event will be thrown, if a staticCacheContext will be reached or will be left
 * 
 * @package extracache
 * @subpackage System_Event_Events
 */
class Tx_Extracache_System_Event_Events_EventOnStaticCacheContext extends Tx_Extracache_System_Event_Events_Event {
	/**
	 * name of the event
	 * @var string
	 */
	protected $name = 'onStaticCacheContext';
	/**
	 * @var boolean
	 */
	private $staticCacheContext;

	/**
	 * override constructor
	 */
	public function __construct(){}
	/**
	 * @return boolean
	 */
	public function getStaticCacheContext() {
		return $this->staticCacheContext;
	}
	/**
	 * @param	boolean $staticCacheContext
	 * @return	Tx_Extracache_System_Event_Events_EventOnStaticCacheContext
	 */
	public function setStaticCacheContext($staticCacheContext) {
		$this->staticCacheContext = $staticCacheContext;
		return $this;
	}
}