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
 * this event should be thrown (in third-party-extensions), if the request should not be cached, because the page is
 * faulty (e.g. an error/exception occured, so the generated page maybe contains a warning, which you don't want to cache statically)
 * 
 * @package extracache
 * @subpackage System_Event_Events
 */
class Tx_Extracache_System_Event_Events_EventOnFaultyPages extends Tx_Extracache_System_Event_Events_Event {
	/**
	 * override constructor
	 */
	public function __construct() {
		parent::__construct('onFaultyPages', null);
	}
}