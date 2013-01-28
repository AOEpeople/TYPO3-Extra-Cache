<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 AOE media GmbH <dev@aoemedia.de>
*  All rights reserved
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once t3lib_extMgm::extPath ( 'nc_staticfilecache' ,'infomodule/class.tx_ncstaticfilecache_infomodule.php') ;

/**
 * @package extracache
 */
class ux_tx_ncstaticfilecache_infomodule extends tx_ncstaticfilecache_infomodule {
	/**
	 * @var	string
	 */
	private $linkIcon;
	/**
	 * handle actions
	 */
	protected function handleActions() {
		$action = t3lib_div::_GP('ACTION');
		$pageId = (integer) t3lib_div::_GP('id');

		if (isset($action['uxMarkAllDirty'])) {
			$this->uxMarkDirty();
		} elseif(isset($action['uxRecachePageOnBaseOfCacheStrategies']) && $this->hasPageWithCacheCleanerStrategy($pageId)) {
			$this->getCacheCleanerBuilder()->buildCacheCleanerForPage( $this->getPageWithCacheCleanerStrategy( $pageId ) )->process();
		} elseif (isset($action['uxUpdateCache']) && t3lib_div::testInt(key($action['uxUpdateCache']))) {
			$this->uxUpdateCache(key($action['uxUpdateCache']));
		} elseif (isset($action['uxMarkDirty']) && t3lib_div::testInt(key($action['uxMarkDirty']))) {
			$this->uxMarkDirty(key($action['uxMarkDirty']));
		} elseif (isset($action['processDirtyPages'])) {
			$this->uxUpdateAllCaches();
		} else {
			parent::handleActions();
		}
	}
	/**
	 * Renders a table row.
	 *
	 * @param	array		$elements The row elements to be rendered
	 * @param	string		$attributes (optional) The attributes to be used on the table row
	 * @param	array		$cacheElement (optional) The cache element row
	 * @return	string		The HTML representation of the table row
	 */
	protected function renderTableRow(array $elements, $attributes = '', array $cacheElement = NULL) {
		if (isset($cacheElement)) {
			// Render groups
			$elements[] = '<td>'.$cacheElement['tx_extracache_grouplist'].'</td>';
			// Render URL:
			$elements[] = '<td align="center">' . $this->wrapWithLinkToCacheElement($this->getLinkIcon(), $cacheElement) . '</td>';
			// Render action links:
			$elements[] = '<td nowrap="nowrap">' . $this->uxGetActions($cacheElement) . '</td>';
			// Render URI:
			$elements[] = '<td nowrap="nowrap">' . $cacheElement['uri'] . '</td>';
		} else {
			$elements[] = '<td> </td>';
			$elements[] = '<td> </td>';
			$elements[] = '<td> </td>';
			$elements[] = '<td> </td>';
		}

		return parent::renderTableRow($elements, $attributes, $cacheElement);
	}
	/**
	 * Renders a table header row.
	 *
	 * @param	array		$elements The row elements to be rendered
	 * @param	string		$attributes (optional) The attributes to be used on the table row
	 * @return	string		The HTML representation of the table row
	 */
	protected function renderTableHeaderRow(array $elements, $attributes = '') {
		$elements[] = '<td>Groups:</td>';
		$elements[] = '<td>URL:</td>';
		$elements[] = '<td>Page Actions:</td>';
		$elements[] = '<td>URI:</td>';
		return parent::renderTableHeaderRow($elements, $attributes);
	}
	/**
	 * Gets the header actions buttons to be rendered in the header section.
	 *
	 * @return	array		Action buttons to be rendered in the header section
	 */
	protected function getHeaderActionButtons() {
		$actionButtons = parent::getHeaderActionButtons();

		if ($this->isMarkDirtyInsteadOfDeletionDefined()) {
			$actionButtons[] = $this->renderActionButton('uxMarkAllDirty', 'Mark all pages dirty', 'Are you sure?');
		}
		if($this->hasPageWithCacheCleanerStrategy( $this->pageId )) {
			$actionButtons[] = $this->renderActionButton('uxRecachePageOnBaseOfCacheStrategies', 'Recache page (on base of defined cache strategies)', 'Are you sure?');
		}
		return $actionButtons;
	}

	/**
	 * Gets specific actions for an element in the cache.
	 *
	 * @param	array		$cacheElement Cache element record
	 * @return	string		HTML representation of action buttons
	 */
	private function uxGetActions(array $cacheElement) {
		$pageId = $cacheElement['pid'];
		$elementId = $cacheElement['uid'];

		return
			$this->renderActionButton('uxUpdateCache][' . $elementId, 'update cache') .
			($this->isMarkDirtyInsteadOfDeletionDefined() && !$cacheElement['isdirty'] ? $this->renderActionButton('uxMarkDirty][' . $elementId, 'mark dirty', 'Are you sure?') : '');
	}
	/**
	 * Marks an element dirty.
	 *
	 * @param	integer		$elementId The element id to be used
	 * @return	void
	 */
	private function uxMarkDirty($elementId = NULL) {
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
			$this->getStaticFileCacheInstance()->getFileTable(),
			($elementId ? 'uid=' . intval($elementId) : ''),
			array('isdirty' => 1)
		);
	}
	/**
	 * Updates the cache a specific element.
	 *
	 * @param	integer		$elementId The element id to be used
	 * @return	void
	 */
	private function uxUpdateCache($elementId) {
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			$this->getStaticFileCacheInstance()->getFileTable(),
			'uid=' . intval($elementId)
		);
		foreach ($rows as $dirtyElement) {
			$this->getStaticFileCacheInstance()->processDirtyPagesElement($dirtyElement);
		}
	}
	/**
	 * Updates the cache of a page (50 per run)
	 * @return	void
	 */
	private function uxUpdateAllCaches() {
		$pageId=t3lib_div::_GP('id');
		if (!is_numeric($pageId)) {
			throw new Exception('pageid not set');
		}
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			$this->getStaticFileCacheInstance()->getFileTable(),
			'pid=' . intval($pageId),
			'','uri ASC','50'
		);
		foreach ($rows as $dirtyElement) {
			$this->getStaticFileCacheInstance()->processDirtyPagesElement($dirtyElement);
		}
	}
	
	/**
	 * @return Tx_Extracache_Domain_Service_CacheCleanerBuilder
	 */
	private function getCacheCleanerBuilder() {
		return t3lib_div::makeInstance('Tx_Extracache_Domain_Service_CacheCleanerBuilder');
	}
	/**
	 * Gets the icon image tag used to visualize a link.
	 *
	 * @return	string		The icon image tag
	 */
	private function getLinkIcon() {
		if (!isset($this->linkIcon)) {
			$this->linkIcon = '<img' . t3lib_iconWorks::skinImg($this->backPath, 'gfx/link_popup.gif') . ' hspace="5" />';
		}
		return $this->linkIcon;
	}
	/**
	 * @param integer $pageId
	 * @return array
	 */
	private function getPageWithCacheCleanerStrategy($pageId) {
		return $this->getTypo3DbBackend()->getPageWithCacheCleanerStrategyForPageId( $pageId );
	}
	/**
	 * @return Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	private function getTypo3DbBackend() {
		return t3lib_div::makeInstance('Tx_Extracache_System_Persistence_Typo3DbBackend');
	}
	/**
	 * @param integer $pageId
	 * @return boolean
	 */
	private function hasPageWithCacheCleanerStrategy($pageId) {
		if(empty($pageId)) {
			return FALSE;
		}
		return ($this->getPageWithCacheCleanerStrategy($pageId) !== NULL);
	}
	/**
	 * Wraps content with the original link of the cache element.
	 *
	 * @param	string		$content Content to be wrapped
	 * @param	array		$cacheElement The cache element row
	 * @param	string		$target (optional) The link target (default: '_blank')
	 * @return	string		The content wrapped with the link
	 */
	private function wrapWithLinkToCacheElement($content, array $cacheElement, $target = '_blank') {
		return '<a href="' . htmlspecialchars('http://' . $cacheElement['host'] . $cacheElement['uri']) . '"' .
			($target ? ' target="' . htmlspecialchars($target) . '"' : '') . '>' . $content . '</a>';
	}
}