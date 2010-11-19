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
 * this event will be thrown, if the request could be respond by staticCacheDispatcher and some other extensions want to modify the response before the response will be send to the client
 * 
 * @package extracache
 * @subpackage System_Event_Events
 */
class Tx_Extracache_System_Event_Events_EventOnStaticCacheResponsePostProcess extends Tx_Extracache_System_Event_Events_Event {
	/**
	 * name of the event
	 * @var string
	 */
	protected $name = 'onStaticCacheResponsePostProcess';
	/**
	 * @var tslib_feUserAuth
	 */
	private $frontendUser;
	/**
	 * @var Tx_Extracache_System_StaticCache_Response
	 */
	private $response;

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
	 * @return Tx_Extracache_System_StaticCache_Response
	 */
	public function getResponse() {
		return $this->response;
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
	 * @param	Tx_Extracache_System_StaticCache_Response $response
	 * @return	Tx_Extracache_System_Event_Events_EventOnStaticCacheResponsePostProcess
	 */
	public function setResponse(Tx_Extracache_System_StaticCache_Response $response) {
		$this->response = $response;
		return $this;
	}
}