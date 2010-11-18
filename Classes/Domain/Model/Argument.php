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
 * entity-object
 * @package extracache
 */
class Tx_Extracache_Domain_Model_Argument {
	/**
	 * define arguments, which should be ignored if staticCache should be written or readed in:
	 *  - Classes/Typo3/Hooks/StaticFileCache/CreateFileHook.php
	 *  - Classes/System/StaticCache/AbstractManager.php
	 */
	const TYPE_ignoreOnCreatingCache = 'ignoreOnCreatingCache';
	/**
	 * define arguments, which makes sure that requests can't be processed/responsed by staticCache in: 
	 *  - Classes/System/StaticCache/EventHandler.php
	 */
	const TYPE_unprocessible = 'unprocessible';
	/**
	 * define arguments, which should be saved if staticCache will be written and restored if request will be processed/responsed by staticCache in:
	 *  - Classes/Typo3/Hooks/StaticFileCache/CreateFileHook.php
	 */
	const TYPE_whitelist = 'whitelist';
	/**
	 * 
	 */
	const TYPE_frontendConfig = 'frontendConfig';

	/**
	 * @var string
	 */
	private $name;
	/**
	 * @var string
	 */
	private $type;
	/**
	 * @var mixed
	 */
	private $value;
	
	/**
	 * @param string $name
	 * @param string $type
	 * @param mixed $value
	 */
	public function __construct($name, $type, $value) {
		$this->name = $name;
		$this->type = $type;
		$this->value = $value;
	}
	
	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}
	/**
	 * @return mixed
	 */
	public function getValue() {
		return $this->value;
	}
	/**
	 * @return array
	 */
	static public function getSupportedTypes() {
		return array( self::TYPE_ignoreOnCreatingCache, self::TYPE_unprocessible, self::TYPE_whitelist, self::TYPE_frontendConfig);
	}
}