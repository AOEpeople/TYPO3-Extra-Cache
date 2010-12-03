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
	 * @param string $key
	 * @param string $name
	 */
	public function __construct($key, $name) {
		$this->key = $key;
		$this->name = $name;
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