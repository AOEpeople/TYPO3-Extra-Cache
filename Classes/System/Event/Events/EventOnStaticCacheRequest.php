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
 * this event will be thrown, when we must check, if we can respond the request in:
 *  - Tx_Extracache_System_StaticCache_AbstractManager
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
	 * @var string
	 */
	private $reasonForCancelation;

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
	 * @return string
	 */
	public function getReasonForCancelation() {
		return $this->reasonForCancelation;
	}

	/**
	 * @param	tslib_feUserAuth $frontendUser
	 * @return	Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest
	 */
	public function setFrontendUser(tslib_feUserAuth $frontendUser) {
		$this->frontendUser = $frontendUser;
		return $this;
	}
	/**
	 * @param	Tx_Extracache_System_StaticCache_Request $request
	 * @return	Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest
	 */
	public function setRequest(Tx_Extracache_System_StaticCache_Request $request) {
		$this->request = $request;
		return $this;
	}
	/**
	 * @param	string $reasonForCancelation
	 * @return	Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest
	 */
	public function setReasonForCancelation($reasonForCancelation) {
		$this->reasonForCancelation = $reasonForCancelation;
		return $this;
	}
}