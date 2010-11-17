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
 * this event will be thrown, if the system will be halt because the request could be respond by staticCacheDispatcher
 * 
 * @package extracache
 * @subpackage System_Event_Events
 */
class Tx_Extracache_System_Event_Events_EventOnStaticCacheResponseHalt extends Tx_Extracache_System_Event_Events_Event {
	/**
	 * name of the event
	 * @var string
	 */
	protected $name = 'onStaticCacheResponseHalt';

	/**
	 * override constructor
	 */
	public function __construct(){}
}