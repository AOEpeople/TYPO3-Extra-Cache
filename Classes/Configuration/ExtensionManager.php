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
 */
class Tx_Extracache_Configuration_ExtensionManager implements t3lib_Singleton {
	/**
	 * @var array
	 */
	private $configuration=array();

	/**
	 * constructor - loading the current localconf configuration for eft extension
	 *
	 */
	public function __construct() {
		$this->configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['extracache']);
	}

	/**
	 * returns configurationvalue for the given key
	 *
	 * @param string $key
	 * @return string/boolean	depending on configuration key
	 */
	public function get($key) {
		return $this->configuration[$key];
	}
}