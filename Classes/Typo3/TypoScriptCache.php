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
 * Attention: this class-name must begin with 'tx' and NOT with 'Tx'...otherwise this hook will not work!
 *
 * @package extracache
 * @subpackage Typo3
 */
class tx_Extracache_Typo3_TypoScriptCache implements t3lib_Singleton {
	const EVENT_Generate = 'onTypoScriptCacheGenerate';

	/**
	 * @var Tx_Extracache_System_Event_Dispatcher
	 */
	private $eventDispatcher;
	/**
	 * @var	boolean
	 */
	private $isRestored = false;

	/**
	 * delete TypoScript-Cache if cacheCmd is 'all', 'pages' or an pageId
	 *
	 * @param array $params
	 * @return void
	 */
	public function clearCachePostProc(array $params) {
		if (in_array($params['cacheCmd'], array('all', 'pages'))) {
			t3lib_div::rmdir($this->getCacheFolder(), TRUE);
		} elseif (t3lib_div::testInt( $params['cacheCmd'] )) {
			$cacheFilePath = $this->getCacheFilePath( (integer) $params['cacheCmd'] );
			if (file_exists ( $cacheFilePath )) {
				unlink ( $cacheFilePath );
			}
		}
	}

	/**
	 * Gets the pageId of the current page.
	 *
	 * @param tslib_fe $frontend
	 * @return integer
	 */
	public function getTemplatePageId(tslib_fe $frontend) {
		return $frontend->id;
	}

	/**
	 * @return boolean
	 */
	public function isAvailable() {
		return @is_file($this->getCacheFilePath());
	}

	/**
	 * Resotres the cached TypoScript configuration.
	 *
	 * @return	void
	 */
	public function restore() {
		// Only restore cached TypoScript if the content is cached:
		$frontend = $this->getFrontend();
		if ($frontend->cacheContentFlag && ! $this->isRestored) {
			// Fetch the cached information and restore it:
			$cacheFilePath = $this->getCacheFilePath();
			if (@is_file ( $cacheFilePath )) {
				$cache = unserialize(t3lib_div::getURL($cacheFilePath));
			} else {
				// Generate the cache information:
				$cache = $this->generate ();
			}

			// Merge current TypoScript with cached:
			if (count ( $cache )) {
				$frontend->tmpl->setup = array_merge ( ( array ) $frontend->tmpl->setup, $cache );
				$libraries = $frontend->tmpl->setup['includeLibs.'];
				if(is_array($libraries)) {
					$frontend->includeLibraries( $libraries );
				}
			}

			$this->isRestored = true;
		}
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
	/**
	 * @return tslib_fe
	 */
	protected function getFrontend() {
		return $GLOBALS['TSFE'];
	}

	/**
	 * Generates the TypoScript of the most specific page containing TypoScript templates and
	 * extracts the settings for 'lib.', 'plugin.', 'tt_content' and 'tt_content.'
	 *
	 * @return	array		Extracted TypoScript configurations
	 */
	private function generate() {
		$frontend = $this->getFrontend();
		// Clone the Template rendering object since we don't want to influence the processing:
		/** @var $template t3lib_TStemplate */
		$template = clone $frontend->tmpl;
		$template->start ( $frontend->sys_page->getRootLine($this->getTemplatePageId($frontend)));

		$keysToBeCached = array('config.', 'includeLibs.', 'lib.', 'plugin.', 'tt_content', 'tt_content.');
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
	 * @param integer $templatePageId optional,default is NULL
	 * @return	string
	 */
	private function getCacheFilePath($templatePageId = NULL) {
		if($templatePageId === NULL) {
			$templatePageId = $this->getTemplatePageId( $this->getFrontend() );
		}
		return $this->getCacheFolder () . 'page_' . $templatePageId . '.php';
	}
	/**
	 * @return string
	 */
	private function getCacheFolder() {
		return PATH_site . 'typo3temp/tx_Extracache_Typo3_TypoScriptCache/';
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
}