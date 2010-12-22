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
 * value-object, which defines an event-log
 * 
 * @package extracache
 */
class Tx_Extracache_Domain_Model_EventLog {
	/**
	 * @var Tx_Extracache_Domain_Model_Event
	 */
	private $event;
	/**
	 * @var array
	 */
	private $infos = array();
	/**
	 * @var integer
	 */
	private $startTime;
	/**
	 * @var integer
	 */
	private $stopTime;

	/**
	 * @param Tx_Extracache_Domain_Model_Event $event
	 */
	public function __construct(Tx_Extracache_Domain_Model_Event $event) {
		$this->event = $event;
		$this->startTime = time();
	}

	/**
	 * @param Tx_Extracache_Domain_Model_Info $info
	 */
	public function addInfo(Tx_Extracache_Domain_Model_Info $info) {
		$this->infos[] = $info;
	}

	/**
	 * @return Tx_Extracache_Domain_Model_Event
	 */
	public function getEvent() {
		return $this->event;
	}
	/**
	 * @return array
	 */
	public function getInfos() {
		return $this->infos;
	}
	/**
	 * @return integer
	 */
	public function getStartTime() {
		return $this->startTime;
	}
	/**
	 * @return integer
	 */
	public function getStopTime() {
		return $this->stopTime;
	}

	/**
	 * set stopTime
	 */
	public function setStopTime() {
		$this->stopTime = time();
	}
}