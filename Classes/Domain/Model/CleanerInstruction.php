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
class Tx_Extracache_Domain_Model_CleanerInstruction {
	/**
	 * @var Tx_Extracache_Domain_Model_CleanerStrategy
	 */
	private $cleanerStrategy;
	/**
	 * @var integer
	 */
	private $pageId;
	/**
	 * @var tx_ncstaticfilecache
	 */
	private $staticFileCache;
	/**
	 * @var t3lib_TCEmain
	 */
	private $tceMain;
	
	/**
	 * @param Tx_Extracache_Domain_Model_CleanerStrategy $cleanerStrategy
	 * @param integer $pageId
	 */
	public function __construct(Tx_Extracache_Domain_Model_CleanerStrategy $cleanerStrategy, $pageId) {
		$this->cleanerStrategy = $cleanerStrategy;
		$this->pageId = $pageId;
		$this->staticFileCache = t3lib_div::makeInstance('tx_ncstaticfilecache');
		$this->tceMain = t3lib_div::makeInstance('t3lib_TCEmain');
	}
	/**
	 * process
	 */
	public function process() {
		
	}
}