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
 * entity-object, which defines a cache-event.
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
	 * @param string	$key
	 * @param string	$name
	 * @param integer	$interval
	 */
	public function __construct($key, $name, $interval) {
		$this->key = $key;
		$this->name = $name;
		$this->interval = $interval;
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
}