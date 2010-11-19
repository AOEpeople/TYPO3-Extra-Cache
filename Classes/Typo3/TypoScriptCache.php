<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 AOE media GmbH <dev@aoemedia.de>
 *  All rights reserved
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Handles caches of TypoScript.
 *
 * @package extracache
 * @subpackage Typo3
 */
class Tx_Extracache_Typo3_TypoScriptCache implements t3lib_Singleton {
	const CONFIG_Key = 'Tx_Extracache_Typo3_TypoScriptCache';
	const EVENT_Generate = 'onTypoScriptCacheGenerate';

	/**
	 * @var	boolean
	 */
	private $isRestored = false;

	/**
	 * @var Tx_Extracache_System_Event_Dispatcher
	 */
	private $eventDispatcher;

	/**
	 * @return tslib_fe
	 */
	protected function getFrontend() {
		return $GLOBALS['TSFE'];
	}

	/**
	 * delete TypoScript-Cache if cacheCmd is 'all' or 'pages'
	 *
	 * @param array $params
	 * @return void
	 */
	public function clearCachePostProc(array $params) {
		$cacheFolder = $this->getCacheFolder();
		if (in_array($params['cacheCmd'], array('all', 'pages')) && is_dir ($cacheFolder)) {
			t3lib_div::rmdir($cacheFolder, true);
		}
	}
	/**
	 * Resotres the cached TypoScript configuration.
	 *
	 * @return	void
	 */
	public function restore() {
		// Only restore cached TypoScript if the content is cached:
		$frontend = $this->getFrontend();
		if ($frontend->cacheContentFlag && ! $this->isRestored || TRUE) {
			$cacheFilePath = $this->getCacheFilePath();
			// Fetch the cached information and restore it:
			if (@is_file ( $cacheFilePath )) {
				$cache = unserialize(t3lib_div::getURL($cacheFilePath));
			} else {
				// Generate the cache information:
				$cache = $this->generate ();
			}
			// Merge current TypoScript with cached:
			if (count ( $cache )) {
				$frontend->tmpl->setup = array_merge ( ( array ) $frontend->tmpl->setup, $cache );
			}

			$this->isRestored = true;
		}
	}

	public function isAvailable() {
		return @is_file($this->getCacheFilePath());
	}

	/**
	 * Generates the TypoScript of the the most specific page containing TypoScript templates and
	 * extracts the settings for 'lib.', 'tt_content' and 'tt_content.'.
	 *
	 * @return	array		Extracted TypoScript configurations
	 */
	private function generate() {
		$frontend = $this->getFrontend();
		// Clone the Template rendering object since we don't want to influence the processing:
		/** @var $template t3lib_TStemplate */
		$template = clone $frontend->tmpl;
		$template->start ( $frontend->sys_page->getRootLine($this->getTemplatePageId($frontend)));

		$keysToBeCached = array('lib.', 'tt_content', 'tt_content.');
		/** @var $event Tx_Extracache_System_Event_Events_Event */
		$event = t3lib_div::makeInstance('Tx_Extracache_System_Event_Events_Event', self::EVENT_Generate, $this, $keysToBeCached);
		$this->getEventDispatcher()->triggerEvent($event);

		$cache = array();
		foreach ($event->getInfos() as $keyToBeCached) {
			$cache[$keyToBeCached] = $template->setup[$keyToBeCached];
		}

		$this->persistCache($cache);

		return $cache;
	}
	/**
	 * Gets the file path to the cache file.
	 *
	 * @return	string
	 */
	private function getCacheFilePath() {
		return $this->getCacheFolder () . 'page_' . $this->getTemplatePageId($this->getFrontend()) . '.php';
	}
	/**
	 * @return string
	 */
	private function getCacheFolder() {
		return PATH_site . 'typo3temp/Tx_Extracache_Typo3_TypoScriptCache/';
	}
	/**
	 * Persists the cache to the file system.
	 *
	 * @param	array		$cache
	 * @return	void
	 */
	private function persistCache(array $cache) {
		$cacheFolder = $this->getCacheFolder ();
		if (! is_dir ( $cacheFolder )) {
			t3lib_div::mkdir ( $cacheFolder );
		}
		t3lib_div::writeFile ( $this->getCacheFilePath (), serialize ( $cache ) );
	}

	/**
	 * Gets the most specific page id that was used to modify the TypoScript templates.
	 *
	 * @return integer
	 */
	public function getTemplatePageId(tslib_fe $frontend) {
		if ($frontend instanceof Tx_Extracache_Typo3_Frontend) {
			/** @var $frontend Tx_Extracache_Typo3_Frontend */
			$templatePageId = $frontend->getTemplatePageId();
		} elseif ($frontend->cacheContentFlag) {
			$templatePageId = $frontend->config[self::CONFIG_Key];
		} else {
			$hierarchyPageIds = array();
			$absolutePageIds = array();

			foreach ($frontend->tmpl->hierarchyInfoToRoot as $hierarchyInfo) {
				if (t3lib_div::testInt($hierarchyInfo['pid']) && $hierarchyInfo['pid'] > 0) {
					$hierarchyPageIds[] = $hierarchyInfo['pid'];
				}
			}
			foreach ($frontend->tmpl->absoluteRootLine as $absoluteInfo) {
					$absolutePageIds[] = $absoluteInfo['uid'];
			}

			$intersections = array_intersect($absolutePageIds, $hierarchyPageIds);
			$templatePageId = array_shift($intersections);
		}

		return (int)$templatePageId;
	}

	/**
	 * @return Tx_Extracache_System_Event_Dispatcher
	 */
	protected function getEventDispatcher() {
		if ($this->eventDispatcher === NULL) {
			$this->eventDispatcher = t3lib_div::makeInstance('Tx_Extracache_System_Event_Dispatcher');
		}
		return $this->eventDispatcher;
	}
}