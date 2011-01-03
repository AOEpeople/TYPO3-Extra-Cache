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
 * value-object, which defines a cache-cleanerStrategy.
 * A cache-cleanerStrategy defines the actions (e.g. delete or update statically cached content), which should be processed, if a
 * certain cache-event occur. A cache-cleanerStrategy is a property of the TYPO3-record 'pages'.
 * 
 * @package extracache
 */
class Tx_Extracache_Domain_Model_CleanerStrategy {
	/**
	 * children defines handling of page and their subpages:
	 *  - childrenWithParent:	Use page + subpages
	 *  - childrenNoAction:		Only use page, no subpages of a page
	 *  - childrenOnly:			Only use subpages of a page
	 */
	const CONSIDER_ChildrenWithParent = 'ChildrenWithParent';
	const CONSIDER_ChildrenNoAction = 'ChildrenNoAction';
	const CONSIDER_ChildrenOnly = 'ChildrenOnly';

	/**
	 * elements defines handling of page and their variants (variants means: same page with other GET-params)
	 *  - elementsWithParent:	Use page and their variants
	 *  - elementsNoAction:		Only use page, no variants of a page
	 *  - elementsOnly:			Only use variants of a page
	 */
	const CONSIDER_ElementsWithParent = 'ElementsWithParent';
	const CONSIDER_ElementsNoAction = 'ElementsNoAction';
	const CONSIDER_ElementsOnly = 'ElementsOnly';

	/**
	 * actions defines the actions, which should be processed
	 *  - staticUpdate:	Processes static element (will update static element OR remove data from file system and database...this depends on the FE-Usergroups)
	 *  - staticClear:	Processes the clearing of the static cache (will remove data from file system and database)
	 *  - staticDirty:	Processes to mark a static element as dirty
	 *  - TYPO3Clear:	delete TYPO3-cache
	 */
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