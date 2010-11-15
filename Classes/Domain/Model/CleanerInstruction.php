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
	 * @var Tx_Extracache_Persistence_Typo3DbBackend
	 */
	private $typo3DbBackend;
	
	/**
	 * @param tx_ncstaticfilecache $staticFileCache
	 * @param t3lib_TCEmain $tceMain
	 * @param Tx_Extracache_Persistence_Typo3DbBackend $typo3DbBackend 
	 * @param Tx_Extracache_Domain_Model_CleanerStrategy $cleanerStrategy
	 * @param integer $pageId
	 */
	public function __construct(tx_ncstaticfilecache $staticFileCache, t3lib_TCEmain $tceMain, Tx_Extracache_Persistence_Typo3DbBackend $typo3DbBackend, Tx_Extracache_Domain_Model_CleanerStrategy $cleanerStrategy, $pageId) {
		$this->cleanerStrategy = $cleanerStrategy;
		$this->pageId = $pageId;
		$this->staticFileCache = $staticFileCache;
		$this->tceMain = $tceMain;
		$this->typo3DbBackend = $typo3DbBackend;
	}

	/**
	 * Processes this instruction and clear cache as defined.
	 *
	 * @return	void
	 */
	public function process() {
		$pageIds = array ();

		// Fetch sub pages:
		if ($this->getCleanerStrategy()->getChildrenMode() === Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenOnly || $this->getCleanerStrategy()->getChildrenMode() === Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenWithParent) {
			$pageIds = $this->getChildPages ( $this->getPageId() );
		}
		// Add current parent page:
		if ($this->getCleanerStrategy()->getChildrenMode() === Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenNoAction || $this->getCleanerStrategy()->getChildrenMode() === Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenWithParent) {
			array_unshift ( $pageIds, $this->getPageId() );
		}

		foreach ( $pageIds as $pageId ) {
			$this->processPage ( $pageId );
		}
	}

	/**
	 * Processes a single page.
	 *
	 * @param	integer		$pageId
	 * @return	void
	 */
	protected function processPage($pageId) {
		if ($this->getCleanerStrategy()->getActions() & Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_TYPO3Clear) {
			$this->processTYPO3Clear ( $pageId );
		}

		if ($this->getCleanerStrategy()->getActions() & Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_StaticUpdate) {
			$staticElements = $this->getStaticElements ( $pageId, $this->getCleanerStrategy()->getElementsMode() );
			foreach ( $staticElements as $staticElement ) {
				$this->getStaticFileCache()->processDirtyPagesElement($staticElement);
			}
		} elseif ($this->getCleanerStrategy()->getActions() & Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_StaticClear) {
			$staticElements = $this->getStaticElements ( $pageId, $this->getCleanerStrategy()->getElementsMode() );
			foreach ( $staticElements as $staticElement ) {
				$this->processStaticClear ( $staticElement );
			}
		} elseif( ($this->getCleanerStrategy()->getActions() & Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_StaticDirty) && ($this->getCleanerStrategy()->getActions() ^ Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_TYPO3Clear)) {
			$staticElements = $this->getStaticElements ( $pageId, $this->getCleanerStrategy()->getElementsMode() );
			foreach ( $staticElements as $staticElement ) {
				$this->processStaticDirty ( $staticElement );
			}
		}
	}

	/**
	 * Processes the clearing of the static cache.
	 * Will remove files from static caching database and filesystem.
	 *
	 * @param	array		$staticElement
	 * @return	void
	 * @throws	LogicException
	 */
	protected function processStaticClear(array $staticElement) {
		$cacheDirectory = $staticElement ['host'] . '/' . $staticElement ['tx_extracache_grouplist'] . dirname ( $staticElement ['file'] );
		$result = $this->getStaticFileCache()->deleteStaticCacheDirectory ( $cacheDirectory );

		if($result !== FALSE) {
			$this->getTypo3DbBackend()->deleteQuery('tx_ncstaticfilecache_file', 'uid=' . intval ( $staticElement ['uid'] ) );
		} else {
			throw new LogicException('Could not delete static cache directory "' . $cacheDirectory . '"');
		}
	}

	/**
	 * Processes to mark a static element as dirty.
	 *
	 * @param	array		$staticElement
	 * @return	void
	 */
	protected function processStaticDirty(array $staticElement) {
		$this->getTypo3DbBackend()->updateQuery('tx_ncstaticfilecache_file', 'uid=' . intval ( $staticElement ['uid'] ), array ('isdirty' => 1 ));
	}

	/**
	 * Proceses the clearing of the TYPO3 cache.
	 *
	 * NOTE: It's not possible to delete discrete cache elements in TYPO3,
	 * thus elements for the whole page will be cleared.
	 *
	 * @param	integer		$pageId
	 * @return	void
	 */
	protected function processTYPO3Clear($pageId) {
		$this->getTceMain()->clear_cacheCmd ( $pageId );
	}

	/**
	 * Gets all child pages of a given page Id (which doesn't contain this active cleanerStrategy)
	 *
	 * @param	integer		$pageId The page Id to fetch the children from
	 * @return	array
	 */
	protected function getChildPages($pageId) {
		$pages = array ();

		$cleanerStrategy = $this->getCleanerStrategy()->getKey();
		$sqlSelect = 'uid,pid';
		$sqlFrom   = 'pages';
		$sqlWhere  = 'deleted=0 AND hidden=0 AND doktype < 199 AND pid=' . intval ( $pageId );
		$sqlWhere .= " AND (tx_extracache_cleanerstrategies='' OR (tx_extracache_cleanerstrategies!='$cleanerStrategy' AND tx_extracache_cleanerstrategies NOT LIKE '$cleanerStrategy,%' AND tx_extracache_cleanerstrategies NOT LIKE '%,$cleanerStrategy' AND tx_extracache_cleanerstrategies NOT LIKE '%,$cleanerStrategy,%'))";
		$pageReocords = $this->getTypo3DbBackend()->selectQuery($sqlSelect, $sqlFrom, $sqlWhere);
		foreach ( $pageReocords as $pageRecord ) {
			$pages [] = $pageRecord ['uid'];
			$pages = array_merge ( $pages, $this->getChildPages ( $pageRecord ['uid'] ) );
		}

		return array_unique ( $pages );
	}

	/**
	 * Gets the static cache elements accordant to consideration of
	 * child elements on a page (= page variations).
	 *
	 * @param	integer		$pageId
	 * @param	string		$elementsMode
	 * @return	array		All found static cache elements
	 */
	protected function getStaticElements($pageId, $elementsMode = Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsWithParent) {
		$limit = '';
		if ($elementsMode === Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsNoAction) {
			$limit = '1';
		}
		$staticElements = $this->getTypo3DbBackend()->selectQuery('*', 'tx_ncstaticfilecache_file', 'pid=' . intval ( $pageId ), 'uri ASC', $limit);

		// Remove the parent element if only the elements shall be considered:
		if ($elementsMode === Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsOnly) {
			array_shift ( $staticElements );
		}

		return $staticElements;
	}

	/**
	 * @return Tx_Extracache_Domain_Model_CleanerStrategy
	 */
	private function getCleanerStrategy() {
		return $this->cleanerStrategy;
	}
	/**
	 * @return integer
	 */
	private function getPageId() {
		return $this->pageId;
	}
	/**
	 * @return tx_ncstaticfilecache
	 */
	private function getStaticFileCache() {
		return $this->staticFileCache;
	}
	/**
	 * @return t3lib_TCEmain
	 */
	private function getTceMain() {
		return $this->tceMain;
	}
	/**
	 * @return Tx_Extracache_Persistence_Typo3DbBackend
	 */
	private function getTypo3DbBackend() {
		return $this->typo3DbBackend;
	}
}