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
 * @package extracache
 * @subpackage Typo3_Hooks_StaticFileCache
 *
 */
class Tx_Extracache_Typo3_Hooks_StaticFileCache_CreateFileHook extends Tx_Extracache_Typo3_Hooks_StaticFileCache_AbstractHook implements t3lib_Singleton {
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
		$frontend = $this->getFrontend($parameters);

		$frontendUserGroupList = $this->getFrontendUserGroupList($frontend->fe_user);
			// Adds the frontend user groups to the cache directory path:
		$parameters['cacheDir'] .= DIRECTORY_SEPARATOR . $frontendUserGroupList;
			// Adds the frontend user groups to the static cache table:
		$parameters['fieldValues'][Tx_Extracache_Typo3_Hooks_StaticFileCache_AbstractHook::FIELD_GroupList] = $frontendUserGroupList;
			// Defines the additionalHash value for database lookups:
		$parameters['additionalHash'] = md5($frontendUserGroupList);
			// Fixes a non speaking URI request (e.g. /index.php?id=13):
		$this->fixNonSpeakingUri($parameters, $frontend);
			// Modifies the URI to be cached to not contain any unwanted arguments:
		$parameters['uri'] = Tx_Extracache_System_Tools_Uri::filterUriArguments(
			$parameters['uri'], $this->getArgumentRepository()->getArgumentsByType(Tx_Extracache_Domain_Model_Argument::TYPE_ignoreOnCreatingCache)
		);
		$parameters['uri'] = Tx_Extracache_System_Tools_Uri::fixIndexUri($parameters['uri']);
			// @todo Move to EFT
			// Due to the disposal of scheme-specific markers the HTTP-check can be avoided in nc_staticfilecache:
		// $parameters['isHttp'] = TRUE;

			// Avoid writing a static cache file and entry if the page is still anonymous with logged in frontend user:
			// @todo BUFFALO_3-0: Reactivate anonymous page delivery
		/*
			if ($frontendUserGroupList !== '0,-1' && $this->isAnonymous($frontend)) {
				$parameters['staticCacheable'] = FALSE;
				// Override staticCacheable status and recreate with ignoring active frontend users:
			} elseif ($parameters['staticCacheable'] === FALSE) {
		*/

		if ($parameters['staticCacheable'] === FALSE) {
			$parameters['staticCacheable'] = (!$frontend->no_cache && !$frontend->isINTincScript() && !$frontend->isEXTincScript());
		}

			// Override staticCacheable status and check, if request is static cachable for t3blog-extension
		// @todo Move to EFT
//		if($parameters['staticCacheable'] === TRUE) {
//			$parameters['staticCacheable'] = tx_eft_system_IoC_manager::create ( 'tx_eft_system_contentProcessor_t3blogProcessor' )->isStaticCachable();
//		}

		$event = $this->getNewEvent(self::EVENT_Initialize, $parameters, $parent, $frontend);
		$this->getEventDispatcher()->triggerEvent($event);
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
		);

		$event = $this->getNewEvent(self::EVENT_Process, $pageInformation, $parent, $frontend);
		$this->getEventDispatcher()->triggerEvent($event);

		$content = Tx_Extracache_System_StaticCache_AbstractManager::DATA_PageInformationPrefix . serialize($pageInformation) .
				Tx_Extracache_System_StaticCache_AbstractManager::DATA_PageInformationSuffix . "\n" . $parameters['content'];

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
	 * @param array $parameters
	 * @param tslib_fe $frontend
	 * @return void
	 */
	protected function fixNonSpeakingUri(array $parameters, tslib_fe $frontend) {
		$matches = array();

		if ($this->isCrawlerExtensionRunning($frontend) && preg_match('#^/index.php\?&?id=(\d+)$#', $parameters['uri'], $matches)) {
			$speakingUri = $frontend->cObj->typoLink_URL(array('parameter' => $matches[1]));
			$speakingUriParts = parse_url($speakingUri);
			if(FALSE === $speakingUriParts){
				throw new Exception('Could not parse URI: ' . $speakingUri, 1289915976);
			}
			$speakingUrlPath = '/' . ltrim($speakingUriParts['path'], '/');
				// Don't change anything if speaking URL is part of old URI:
				// (it might be the case the using the speaking URL failed)
			if (strpos($parameters['uri'], $speakingUrlPath) !== 0 || $speakingUrlPath === '/') {
				if (isset($speakingUriParts['host'])) {
					$parameters['host'] = $speakingUriParts['host'];
				}

				$parameters['uri'] = $speakingUrlPath;
			}
		}
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
	 * Gets the frontend user group list.
	 * CAVE: The anonymous groups (0,-1) and (0,-2) are already prepended!
	 *
	 * @return string
	 */
	protected function getFrontendUserGroupList(tslib_feUserAuth $frontendUser) {
		if (is_array($frontendUser->user) && count($frontendUser->groupData['uid'])) {
			$frontendUserGroups = array_unique($frontendUser->groupData['uid']);
			sort($frontendUserGroups);
			$frontendUserGroupList = '0,-2,' . implode(',', $frontendUserGroups);
		} else {
			$frontendUserGroupList = '0,-1';
		}

		return $frontendUserGroupList;
	}
}