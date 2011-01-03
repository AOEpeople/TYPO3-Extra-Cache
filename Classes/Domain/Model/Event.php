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
 * value-object, which defines a cache-event.
 * A cache-event can be triggered to start processing cache-cleanerStrategies. A cache-event is a property of the TYPO3-record 'pages'.
 * 
 * @package extracache
 */
class Tx_Extracache_Domain_Model_Event {
	/**
	 * @var string
	 */
	private $key;
	/**
	 * @var string
	 */
	private $name;
	/**
	 * define, when the event should be processed (0 ==> instantly, otherwise at the earliest in X seconds)
	 * 
	 * @var integer
	 */
	private $interval;
	/**
	 * define, if a log should be written, after the event was processed
	 * 
	 * @var boolean
	 */
	private $writeLog;

	/**
	 * @param string	$key
	 * @param string	$name
	 * @param integer	$interval
	 */
	public function __construct($key, $name, $interval, $writeLog) {
		$this->key = $key;
		$this->name = $name;
		$this->interval = $interval;
		$this->writeLog = $writeLog;
	}

	/**
	 * @return integer
	 */
	public function getInterval() {
		return $this->interval;
	}
	/**
	 * @return string
	 */
	public function getKey() {
		return $this->key;
	}
	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	/**
	 * @return boolean
	 */
	public function getWriteLog() {
		return $this->writeLog;
	}
}