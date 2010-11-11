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
 */
class Tx_Extracache_Domain_Model_Argument {
	const TYPE_ignoreOnCreatingCache = 'ignoreOnCreatingCache';
	const TYPE_unprocessible = 'unprocessible';
	const TYPE_whitelist = 'whitelist';

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
		return array( self::TYPE_ignoreOnCreatingCache, self::TYPE_unprocessible, self::TYPE_whitelist);
	}
}