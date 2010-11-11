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
class Tx_Extracache_Domain_Model_CleanerStrategy {
	const CONSIDER_ChildrenWithParent = 'ChildrenWithParent';
	const CONSIDER_ChildrenNoAction = 'ChildrenNoAction';
	const CONSIDER_ChildrenOnly = 'ChildrenOnly';

	const CONSIDER_ElementsWithParent = 'ElementsWithParent';
	const CONSIDER_ElementsNoAction = 'ElementsNoAction';
	const CONSIDER_ElementsOnly = 'ElementsOnly';

	const ACTION_None = 2048;
	const ACTION_StaticUpdate = 64;
	const ACTION_StaticClear = 32;
	const ACTION_StaticDirty = 16;
	const ACTION_TYPO3Clear = 1;
	
	/**
	 * @var integer
	 */
	private $actions;
	/**
	 * @var string
	 */
	private $childrenMode;
	/**
	 * @var string
	 */
	private $elementsMode;
	/**
	 * @var string
	 */
	private $key;
	/**
	 * @var string
	 */
	private $name;

	/**
	 * @param integer $actions
	 * @param string $childrenMode
	 * @param string $elementsMode
	 * @param string $key
	 * @param string $name
	 */
	public function __construct($actions, $childrenMode, $elementsMode, $key, $name) {
		$this->actions = $actions;
		$this->childrenMode = $childrenMode;
		$this->elementsMode = $elementsMode;
		$this->key = $key;
		$this->name = $name;
	}
	
	/**
	 * @return integer
	 */
	public function getActions() {
		return $this->actions;
	}
	/**
	 * @return string
	 */
	public function getChildrenMode() {
		return $this->childrenMode;
	}
	/**
	 * @return string
	 */
	public function getElementsMode() {
		return $this->elementsMode;
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
	 * @return array
	 */
	static public function getSupportedChildModes() {
		return array( self::CONSIDER_ChildrenWithParent, self::CONSIDER_ChildrenNoAction,self::CONSIDER_ChildrenOnly);
	}
	/**
	 * @return array
	 */
	static public function getSupportedElementModes() {
		return array( self::CONSIDER_ElementsWithParent, self::CONSIDER_ElementsNoAction, self::CONSIDER_ElementsOnly);
	}
}