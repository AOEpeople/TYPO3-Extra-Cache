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
 * @subpackage System_Event_Events
 */
class Tx_Extracache_System_Event_Events_EventOnStaticCacheResponsePostProcess extends Tx_Extracache_System_Event_Events_Event {
	/**
	 * name of the event
	 * @var string
	 */
	protected $name = 'onStaticCacheResponsePostProcess';
	/**
	 * @var Tx_Extracache_System_StaticCache_Response
	 */
	private $response;

	/**
	 * override constructor
	 */
	public function __construct(){}
	/**
	 * @return Tx_Extracache_System_StaticCache_Response
	 */
	public function getResponse() {
		return $this->response;
	}
	/**
	 * @param Tx_Extracache_System_StaticCache_Response $response
	 */
	public function setResponse(Tx_Extracache_System_StaticCache_Response $response) {
		$this->response = $response;
	}
}