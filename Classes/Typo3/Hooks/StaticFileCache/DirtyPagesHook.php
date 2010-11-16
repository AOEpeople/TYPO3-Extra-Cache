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
 * Hook for nc_staticfilecache that is called on processing dirty pages.
 *
 * @package extracache
 * @subpackage Typo3_Hooks_StaticFileCache
 *
 */
class Tx_Extracache_Typo3_Hooks_StaticFileCache_DirtyPagesHook extends Tx_Extracache_Typo3_Hooks_StaticFileCache_AbstractHook implements t3lib_Singleton {
	const EVENT_Process = 'onStaticFileCacheDirtyPagesProcess';
	const HTTP_Request_Header = 'X-PROCESS-DIRTY-PAGES';

	/**
	 * @var t3lib_cli
	 */
	protected $cliDispatcher;
	/**
	 * define the allready removed caches (key is: [pageId]_[groupList])
	 * @var array
	 */
	private $removedCaches = array();

	/**
	 * Processes elements after being removed from filesystem.
	 * Tries to recache static cached files.
	 *
	 * Attention: This class gets called in CLI context only!
	 *
	 * @param	array					$parameters The parameters used in this hook
	 * @param	tx_ncstaticfilecache	$parent The calling parent object
	 * @return	void
	 */
	public function process(array $parameters, tx_ncstaticfilecache $parent) {
		$this->cliDispatcher = $parameters['cliDispatcher'];

		$event = $this->getNewEvent(self::EVENT_Process, $parameters, $parent);
		$this->getEventDispatcher()->triggerEvent($event);

		if ($event->isCanceled() === TRUE) {
			return;
		}

		$dirtyElement = $parameters['dirtyElement'];
		$groupList = $dirtyElement[self::FIELD_GroupList];

		$parameters['cacheDirectory'] = $this->getStaticCacheDirectory($dirtyElement);

			// Only recache if the frontend user group is anonymous or none:
		if (!$groupList || $groupList === '0,-1') {
			try {
				if ($this->recacheElement($dirtyElement, $parent) === TRUE) {
					$parameters['cancelExecution'] = TRUE;
				}
			} catch (Exception $e) {
				t3lib_div::devLog(
					'Exception: ' . $exception->getMessage().' / '.$exception->getTraceAsString(), __CLASS__
				);
			}

			// Remove the TYPO3 caches if there is a real frontend user group:
		} else {
			$this->removeCaches($dirtyElement['pid'], $groupList);
		}
	}

	/**
	 * Recaches the contents of an element.
	 *
	 * @param array $dirtyElement
	 * @param tx_ncstaticfilecache $parent
	 * @return void
	 */
	protected function recacheElement(array $dirtyElement, tx_ncstaticfilecache $parent) {
		$absolutePath = PATH_site . $parent->getCacheDirectory() . $this->getStaticCacheDirectory($dirtyElement) . '/index.html';

		$fileTimeBefore = $this->getFileModificationTime($absolutePath);
		$this->fetchUrl($dirtyElement['host'], $dirtyElement['uri']);
		$fileTimeAfter = $this->getFileModificationTime($absolutePath);

		$isRecached = ($fileTimeAfter > $fileTimeBefore || $fileTimeBefore === FALSE && $fileTimeAfter !== FALSE);
		return $isRecached;
	}

	/**
	 * Fetches the content of an URL.
	 *
	 * @param string $host
	 * @param string $uri
	 * @return boolean
	 * @throws Exception
	 */
	protected function fetchUrl($host, $uri) {
		if(empty($host)){
			throw new Exception('invalid host given');
		}
		if(empty($uri)){
			throw new Exception('invalid uri given');
		}

		$urlToFetch = 'http://' . $host . $uri;
		$requestHeaders = array(self::HTTP_Request_Header . ': 1');

		if ($this->cliDispatcher) {
			$this->cliDispatcher->cli_echo('Re-caching ' . $urlToFetch . '... ');
		}

		if (t3lib_extMgm::isLoaded('directrequest')) {
			/* @var $directRequestManager tx_directrequest_manager */
			$directRequestManager = t3lib_div::makeInstance('tx_directrequest_manager');
			$response = $directRequestManager->execute($urlToFetch, $requestHeaders);
			$result = $response['content'];
		} else {
			$result = t3lib_div::getURL($urlToFetch, 0, $requestHeaders);
		}
		$result = (bool) trim( $result ); // if fetching failed, it can happen, that the returned result contains some space characters!

		if ($this->cliDispatcher) {
			$this->cliDispatcher->cli_echo(($result ? 'OK' : 'FAILED') . PHP_EOL);
		}

		if($result === FALSE) {
			t3lib_div::devLog('Re-caching ' . $urlToFetch . ' failed', __CLASS__);
		}

		return $result;
	}

	/**
	 * Removes the TYPO3 caches for a specific pageId and groupList.
	 *
	 * @param integer $pageId
	 * @param integer $groupList
	 * @return void
	 */
	protected function removeCaches($pageId, $groupList) {
		// remove each cache-entry for pageId X and groupList Y only one-time!
		// @todo Integrate logic for using the Caching Framework on Core Caches
		$cacheKey = $pageId.'_'.$groupList;
		if(!in_array($cacheKey, $this->removedCaches)) {
			$GLOBALS['TYPO3_DB']->exec_DELETEquery(
				'cache_pages',
				self::FIELD_GroupList . '=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($groupList, 'cache_pages') .
				' AND page_id=' . intval($pageId)
			);
			$this->removedCaches[] = $cacheKey;
		}
	}

	/**
	 * Gets the relative cache directory for a dirty element
	 *
	 * @param array $dirtyElement
	 * @return string
	 */
	protected function getStaticCacheDirectory(array $dirtyElement) {
		return $dirtyElement['host'] . '/' . $dirtyElement[self::FIELD_GroupList] . dirname($dirtyElement['file']);
	}

	/**
	 * Gets the file modification time.
	 *
	 * @param string $fileName
	 * @return mixed
	 */
	protected function getFileModificationTime($fileName) {
		$result = FALSE;

		if (is_file($fileName)) {
			clearstatcache();
			$result = filemtime($fileName);
		}

		return $result;
	}
}