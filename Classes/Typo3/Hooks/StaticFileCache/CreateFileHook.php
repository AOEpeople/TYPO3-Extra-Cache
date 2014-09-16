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
 * Hook for nc_staticfilecache that is called on creating the file with the cached content.
 * 
 * Attention: this class-name must begin with 'tx' and NOT with 'Tx'...otherwise this hook will not work!
 *
 * @package extracache
 * @subpackage Typo3_Hooks_StaticFileCache
 *
 */
class tx_Extracache_Typo3_Hooks_StaticFileCache_CreateFileHook extends Tx_Extracache_Typo3_Hooks_StaticFileCache_AbstractHook implements t3lib_Singleton {
	const EVENT_PreInitialize = 'onStaticFileCacheCreateFilePreInitialize';
	const EVENT_Initialize = 'onStaticFileCacheCreateFileInitialize';
	const EVENT_Process = 'onStaticFileCacheCreateFileProcess';

	/**
	 * Initializes the variables before starting the processing.
	 *
	 * @param	array					$parameters The parameters used in this hook
	 * @param	tx_ncstaticfilecache	$parent The calling parent object
	 * @return	void
	 */
	public function initialize(array $parameters, tx_ncstaticfilecache $parent) {
		$event = $this->getNewEvent(self::EVENT_PreInitialize, $parameters, $parent);
		$this->getEventDispatcher()->triggerEvent($event);

		if($this->isUnprocessibleRequestAction()) {
			// change parameters, so nc_staticfilecache will NOT cache this request
			$parameters['isHttp'] = FALSE;
			$parameters['staticCacheable'] = FALSE;
		} else {
			$frontend = $this->getFrontend($parameters);

			// modify some data if we support FE-usergroups
			if($this->getExtensionManager()->isSupportForFeUsergroupsSet() === TRUE) {
                /* @var $frontendUser ux_tslib_feUserAuth */
                $frontendUser = $frontend->fe_user;
				$frontendUserGroupList = $frontendUser->getGroupList();

				// Adds the frontend user groups to the cache directory path:
				$parameters['cacheDir'] .= DIRECTORY_SEPARATOR . $frontendUserGroupList;

				// Adds the frontend user groups to the static cache table:
				$parameters['fieldValues'][Tx_Extracache_Typo3_Hooks_StaticFileCache_AbstractHook::FIELD_GroupList] = $frontendUserGroupList;

				// Defines the additionalHash value for database lookups:
				$parameters['additionalHash'] = md5($frontendUserGroupList);
			}

			// Fixes a non speaking URI request (e.g. /index.php?id=13):
			list($parameters['host'], $parameters['uri']) = $this->fixNonSpeakingUri($parameters['host'], $parameters['uri'], $frontend);

			// Modifies the URI to be cached to not contain any unwanted arguments:
			$parameters['uri'] = Tx_Extracache_System_Tools_Uri::filterUriArguments(
				$parameters['uri'], $this->getArgumentRepository()->getArgumentsByType(Tx_Extracache_Domain_Model_Argument::TYPE_ignoreOnCreatingCache)
			);
			$parameters['uri'] = Tx_Extracache_System_Tools_Uri::fixIndexUri($parameters['uri']);

			// Avoid writing a static cache file and entry if the page is still anonymous with logged in frontend user:
			// @todo BUFFALO_3-0: Reactivate anonymous page delivery
			/*
				if ($frontendUserGroupList !== '0,-1' && $this->isAnonymous($frontend)) {
					$parameters['staticCacheable'] = FALSE;
					// Override staticCacheable status and recreate with ignoring active frontend users:
				} elseif ($parameters['staticCacheable'] === FALSE) {
			*/

			$event = $this->getNewEvent(self::EVENT_Initialize, $parameters, $parent, $frontend);
			$this->getEventDispatcher()->triggerEvent($event);
		}
	}

	/**
	 * Processes the content before writing to the static cache directory.
	 *
	 * @param	array					$parameters The parameters used in this hook
	 * @param	tx_ncstaticfilecache	$parent The calling parent object
	 * @return	string					The modified content
	 */
	public function process(array $parameters, tx_ncstaticfilecache $parent) {
		$frontend = $this->getFrontend($parameters);

		$pageInformation = array(
			'id' => $frontend->id,
			'type' => $frontend->type,
			'MP' => $frontend->MP,
			'config' => array(
				'config' => $frontend->config['config'],
			),
			'GET' => $this->getWhiteListedArguments(),
			'isAnonymous' => $this->isAnonymous($frontend),
			'firstRootlineId' => (isset($frontend->rootLine[0]['uid']) ? $frontend->rootLine[0]['uid'] : NULL),
			'content' => $parameters['content']
		);

		// trigger event
		$event = $this->getNewEvent(self::EVENT_Process, $pageInformation, $parent, $frontend);
		$pageInformation = $this->getEventDispatcher()->triggerEvent($event)->getInfos();

		// get content from page-infos and unset it in the page-infos-array 
		$content = $pageInformation['content'];
		unset($pageInformation['content']);

		$content = Tx_Extracache_System_StaticCache_AbstractManager::DATA_PageInformationPrefix . serialize($pageInformation) .
				Tx_Extracache_System_StaticCache_AbstractManager::DATA_PageInformationSuffix . "\n" . $content;

		return $content;
	}

	/**
	 * Gets the whitelisted GET arguments that shall be published.
	 *
	 * @return	array		Whitelisted GET arguments that shall be published
	 */
	protected function getWhiteListedArguments() {
		$arguments = array();
		$getArguments = $this->getGetArguments();
		$whitelistArguments = $this->getArgumentRepository()->getArgumentsByType(Tx_Extracache_Domain_Model_Argument::TYPE_whitelist);

		/** @var $whitelistArgument Tx_Extracache_Domain_Model_Argument */
		foreach ($whitelistArguments as $whitelistArgument) {
			$name = $whitelistArgument->getName();
			$definition = $whitelistArgument->getValue();

			if (isset($getArguments[$name])) {
				if (is_array($definition)) {
					$subArguments = array();
					foreach ($definition as $subName) {
						if (isset($getArguments[$name][$subName])) {
							$subArguments[$subName] = $getArguments[$name][$subName];
						}
					}
					if (count($subArguments)) {
						$arguments[$name] = $subArguments;
					}
				} elseif($definition) {
					$arguments[$name] = $getArguments[$name];
				}
			}
		}

		return $arguments;
	}

	/**
	 * Gets the GET arguments.
	 *
	 * @return array
	 */
	protected function getGetArguments() {
		return $_GET;
	}

	/**
	 * Fixes non speaking URLs.
	 *
	 * @param	string $host
	 * @param	string $uri
	 * @param	tslib_fe $frontend
	 * @return	array
	 */
	protected function fixNonSpeakingUri($host, $uri, tslib_fe $frontend) {
		$matches = array();

		if ($this->isCrawlerExtensionRunning($frontend) && preg_match('#^/index.php\?&?id=(\d+)$#', $uri, $matches)) {
			$speakingUri = $frontend->cObj->typoLink_URL(array('parameter' => $matches[1]));
			$speakingUriParts = parse_url($speakingUri);
			if(FALSE === $speakingUriParts){
				throw new Exception('Could not parse URI: ' . $speakingUri, 1289915976);
			}
			$speakingUrlPath = '/' . ltrim($speakingUriParts['path'], '/');
				// Don't change anything if speaking URL is part of old URI:
				// (it might be the case the using the speaking URL failed)
			if (strpos($uri, $speakingUrlPath) !== 0 || $speakingUrlPath === '/') {
				if (isset($speakingUriParts['host'])) {
					$host = $speakingUriParts['host'];
				}

				$uri = $speakingUrlPath;
			}
		}

		return array($host, $uri);
	}

	/**
	 * Determine whether the crawler extension is running and initiated the current request.
	 *
	 * @param tslib_fe $frontend
	 * @return boolean
	 */
	protected function isCrawlerExtensionRunning(tslib_fe $frontend) {
		return (
			$this->isExtensionLoaded('crawler')
			&& isset($frontend->applicationData['tx_crawler']['running'])
			&& isset($frontend->applicationData['tx_crawler']['parameters']['procInstructions'])
			&& $frontend->applicationData['tx_crawler']['running']
		);
	}

	/**
	 * Determines whether all pages in the rootline and
	 * the content on the current page are anonymous.
	 *
	 * @param tslib_fe $frontend
	 * @return boolean
	 */
	protected function isAnonymous(tslib_fe $frontend) {
		// @todo This feature is currently disabled
		return FALSE;

		foreach ($frontend->rootLine as $page) {
			if (!empty($page['fe_group'])) {
				return FALSE;
			}
		}

		return $this->hasOnlyAnonymousContent($frontend->page['uid']);
	}
	/**
	 * Determines whether the current request cannot be cached staticaly in general.
	 * The behaviour can be configured via creating Tx_Extracache_Domain_Model_Argument-objects with Type Tx_Extracache_Domain_Model_Argument::TYPE_unprocessible
	 *
	 * @return	boolean
	 */
	protected function isUnprocessibleRequestAction() {
		if($this->isBackendUserActive()) {
			return TRUE;
		}
		return Tx_Extracache_System_Tools_Request::isUnprocessibleRequest(
			$this->getGetArguments(),
			$this->getArgumentRepository()->getArgumentsByType( Tx_Extracache_Domain_Model_Argument::TYPE_unprocessible )
		);
	}
	/**
	 * Determines whether all content on a page is anonymous.
	 *
	 * @param integer $pageId
	 * @return boolean
	 */
	protected function hasOnlyAnonymousContent($pageId) {
		// @todo This feature is currently disabled
		return FALSE;

		$count = $GLOBALS['TYPO3_DB']->exec_SELECTcountRows(
			'uid',
			'tt_content',
			'hidden=0 AND deleted=0 AND pid=' . intval($pageId) . ' AND fe_group'
		);

		return ($count === 0 || $count === '0');
	}

	/**
	 * Gets the frontend.
	 *
	 * @param array $parameters
	 * @return tslib_fe
	 */
	protected function getFrontend(array $parameters) {
		return $parameters['TSFE'];
	}

	/**
	 * @return boolean
	 */
	private function isBackendUserActive() {
		if(is_object($GLOBALS['BE_USER']))  {
			return TRUE;
		}
		return FALSE;
	}
}