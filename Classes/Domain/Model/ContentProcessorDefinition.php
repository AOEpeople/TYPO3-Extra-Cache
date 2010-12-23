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
 * value-object, which defines a contentProcessor
 * 
 * @package extracache
 */
class Tx_Extracache_Domain_Model_ContentProcessorDefinition {
	/**
	 * @var string
	 */
	private $className;
	/**
	 * defines the path to the className (must only be defined, if className and path don't use the synthax of extbase)
	 * @var string
	 */
	private $path;

	/**
	 * @param string $className
	 * @param string $path
	 */
	public function __construct($className, $path=NULL) {
		$this->className = $className;
		$this->path = $path;
	}

	/**
	 * @return string
	 */
	public function getClassName() {
		return $this->className;
	}
	/**
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}
}