<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2009 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
/**
 * Database cache entry
 * @package extracache
 */
class Tx_Extracache_Domain_Model_CacheDatabaseEntry {
	/**
	 * @var array
	 */
	private $recordData = array();
	/**
	 * @var array
	 */
	private $recordKeys = array();

	/**
	 * @param	string		$name Name of the called method
	 * @param	array		$arguments Arguments passed to the called method
	 * @return	mixed		The the result of the called method of the wrapped instance
	 */
	public function __call($name, array $arguments) {
		if(substr($name,0,3) === 'get') {
			$property = strtolower( substr($name,3,strlen($name) ) );
			return $this->recordData[ $property ];
		} elseif(substr($name,0,3) === 'set') {
			$property = strtolower( substr($name,3,strlen($name) ) );
			$this->recordData[ $property ] = $arguments[0];
		}
	}
	
	/**
	 * @return array
	 */
	public function getRecordKeys() {
		return $this->recordKeys;
	}
	/**
	 * @param array $recordKeys
	 */
	public function setRecordKeys($recordKeys) {
		$this->recordKeys = $recordKeys;
	}	
}