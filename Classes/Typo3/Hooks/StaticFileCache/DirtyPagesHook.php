<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 AOE GmbH <dev@aoe.com>
*  All rights reserved
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Hook for nc_staticfilecache that is called on processing dirty pages.
 * 
 * Attention: this class-name must begin with 'tx' and NOT with 'Tx'...otherwise this hook will not work!
 *
 * @package extracache
 * @subpackage Typo3_Hooks_StaticFileCache
 *
 */
class tx_Extracache_Typo3_Hooks_StaticFileCache_DirtyPagesHook extends Tx_Extracache_Typo3_Hooks_StaticFileCache_AbstractHook implements \TYPO3\CMS\Core\SingletonInterface {
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
	 * Determines whether dirty pages are processed.
	 *
	 * @return boolean
	 */
	public function isProcessingDirtyPages() {
		$requestHeader = 'HTTP_' . str_replace('-', '_', self::HTTP_Request_Header);
		$result = (isset($_SERVER[$requestHeader]) && $_SERVER[$requestHeader]);
		return $result;
	}
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
		$groupList = $dirtyElement[Tx_Extracache_Typo3_Hooks_StaticFileCache_AbstractHook::FIELD_GroupList];

		$parameters['cacheDirectory'] = $this->getStaticCacheDirectory($dirtyElement);

		if (!$groupList || $groupList === '0,-1') {
			// Only recache if the frontend user group is anonymous or none
			try {
				if ($this->recacheElement($dirtyElement, $parent) === TRUE) {
					$parameters['cancelExecution'] = TRUE;
				}
			} catch (Exception $e) {
				$message = 'Exception: ' . $e->getMessage().' / '.$e->getTraceAsString();
				$this->getEventDispatcher()->triggerEvent ( 'onStaticCacheWarning', $this, array ('message' => $message ) );
			}
		} else {
			// Remove the TYPO3 caches if there is a real frontend user group
			$this->removeCaches($dirtyElement['pid'], $groupList);
		}
	}

	/**
	 * Recaches the contents of an element.
	 *
	 * @param	array $dirtyElement
	 * @param	tx_ncstaticfilecache $parent
	 * @return	boolean
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
			$directRequestManager = GeneralUtility::makeInstance('tx_directrequest_manager');
			$response = $directRequestManager->execute($urlToFetch, $requestHeaders);
			$statusReport = array();
			if(array_key_exists('error', $response)) {
				$statusReport['error'] = $response['error'];
			}
			$result = $response['content'];
		} else {
			$statusReport = array();
			$result = t3lib_div::getURL($urlToFetch, 1, $requestHeaders, $statusReport);
		}
		$result = (bool) trim( $result ); // if fetching failed, it can happen, that the returned result contains some space characters!

		if ($this->cliDispatcher) {
			$this->cliDispatcher->cli_echo(($result ? 'OK' : 'FAILED') . PHP_EOL);
		}

		if($result === FALSE) {
			// create error-message
			$statusReportInfos = '';
			foreach($statusReport as $key => $value) {
				if($value !== '') {
					if($statusReportInfos !== '') {
						$statusReportInfos .= ',';
					}
					$statusReportInfos .= $key.':'.$value;
				}
			}
			$message = 'Re-caching ' . $urlToFetch . ' failed';
			if($statusReportInfos !== '') {
				$message .= ' ('.$statusReportInfos.')';
			}

			$this->getEventDispatcher()->triggerEvent ( 'onStaticCacheWarning', $this, array ('message' => $message ) );
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
			$sqlFrom = 'cache_pages';
			$sqlWhere = Tx_Extracache_Typo3_Hooks_StaticFileCache_AbstractHook::FIELD_GroupList . '=' . $this->getTypo3DbBackend()->fullQuoteStr($groupList, 'cache_pages') . ' AND page_id=' . intval($pageId);
			$this->getTypo3DbBackend()->deleteQuery($sqlFrom, $sqlWhere);
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
		$cacheDir = $dirtyElement['host'];
		if($this->getExtensionManager()->isSupportForFeUsergroupsSet() === TRUE) {
			$cacheDir .= '/' . $dirtyElement[Tx_Extracache_Typo3_Hooks_StaticFileCache_AbstractHook::FIELD_GroupList];
		}
		$cacheDir .= dirname($dirtyElement['file']);
		return $cacheDir;
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