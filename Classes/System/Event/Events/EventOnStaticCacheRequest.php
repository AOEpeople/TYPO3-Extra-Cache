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
 * this event will be thrown, if a staticCache-Request exists and we must check, if we can respond the request
 * 
 * @package extracache
 * @subpackage System_Event_Events
 */
class Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest extends Tx_Extracache_System_Event_Events_Event {
	/**
	 * name of the event
	 * @var string
	 */
	protected $name = 'onStaticCacheRequest';
	/**
	 * @var tslib_feUserAuth
	 */
	private $frontendUser;
	/**
	 * @var Tx_Extracache_System_StaticCache_Request
	 */
	private $request;

	/**
	 * override constructor
	 */
	public function __construct(){}
	/**
	 * @return tslib_feUserAuth
	 */
	public function getFrontendUser() {
		return $this->frontendUser;
	}
	/**
	 * @return Tx_Extracache_System_StaticCache_Request
	 */
	public function getRequest() {
		return $this->request;
	}
	/**
	 * @param tslib_feUserAuth $frontendUser
	 */
	public function setFrontendUser(tslib_feUserAuth $frontendUser) {
		$this->frontendUser = $frontendUser;
	}
	/**
	 * @param Tx_Extracache_System_StaticCache_Request $request
	 */
	public function setRequest(Tx_Extracache_System_StaticCache_Request $request) {
		$this->request = $request;
	}
}