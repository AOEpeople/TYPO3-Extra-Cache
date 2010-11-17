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

require_once(t3lib_extMgm::extPath ( 'extracache' ) . 'Classes/Typo3/Hooks/StaticFileCache/DirtyPagesHook.php');

/**
 * @package extracache
 * @subpackage System_StaticCache
 */
class Tx_Extracache_System_StaticCache_EventHandler implements t3lib_Singleton {
	/**
	 * @var Tx_Extracache_Domain_Repository_ArgumentRepository
	 */
	private $argumentRepository;
	/**
	 * @var array
	 */
	private $checkMethods;
	/**
	 * @var Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	private $storage;
	
	/**
	 * constructor
	 */
	public function __construct() {
		$checkMethods = array();
		$checkMethods['isProcessingDirtyPages'] = FALSE;
		$checkMethods['isFrontendUserLoggingOut'] = FALSE;
		$checkMethods['isFrontendUserLoggingIn'] = FALSE;
		$checkMethods['isUnprocessibleRequestAction'] = FALSE;
		$checkMethods['isPageMailerExtensionRunning'] = FALSE;
		$checkMethods['isCrawlerExtensionRunning'] = FALSE;
		$checkMethods['isBackendUserActive'] = FALSE;
		$this->setCheckMethods($checkMethods);
	}
	/**
	 * handle event 'onStaticCacheRequest' (this method checks if we can handle the request)
	 * 
	 * @param Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest $event
	 */
	public function handleEventOnStaticCacheRequest(Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest $event) {
		foreach ( $this->getCheckMethods() as $method => $expected ) {
			$checkResult = call_user_func_array ( array ($this, $method ), array ($event) );
			if ($checkResult !== $expected) {
				$event->cancel();
				$event->setReasonForCancelation( 'Check "' . $method . '" prevents from using static caching' );
				break;
			}
		}
	}

	/**
	 * @return Tx_Extracache_Domain_Repository_ArgumentRepository
	 */
	protected function getArgumentRepository() {
		if($this->argumentRepository === NULL) {
			$this->argumentRepository = t3lib_div::makeInstance ( 'Tx_Extracache_Domain_Repository_ArgumentRepository' );
		}
		return $this->argumentRepository;
	}
	/**
	 * @return array
	 */
	protected function getCheckMethods() {
		return $this->checkMethods;
	}
	/**
	 * @return Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	protected function getStorage() {
		if($this->storage === NULL) {
			$this->storage = t3lib_div::makeInstance('Tx_Extracache_System_Persistence_Typo3DbBackend');
		}
		return $this->storage;
	}

	
	/**
	 * Determines whether a valid backend user session is currently active.
	 *
	 * @param	Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest $event
	 * @return	boolean
	 */
	protected function isBackendUserActive(Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest $event) {
		$result = false;
		// @todo:	ADMCMD_prev used for previewing workspaces in front-end is currently not implemented
		if ($event->getRequest()->getCookie ( 'be_typo_user' )) {
			/* @var $backendUser t3lib_tsfeBeUserAuth */
			$backendUser = t3lib_div::makeInstance ( 't3lib_tsfeBeUserAuth' );
			$backendUser->dontSetCookie = true;
			$backendUser->OS = TYPO3_OS;
			$backendUser->lockIP = $GLOBALS ['TYPO3_CONF_VARS'] ['BE'] ['lockIP'];
			$backendUser->start ();

			if (isset ( $backendUser->user ['uid'] ) && $backendUser->user ['uid']) {
				$result = true;
				// @todo: lock to domain is handled in fetchGroupData but needs full initialzed TYPO3 caching framework
				// $backendUser->fetchGroupData();
				if (! $backendUser->checkLockToIP () || ! $backendUser->checkBackendAccessSettingsFromInitPhp ()) {
					$result = false;
				}
			}

			unset ( $backendUser );
		}

		return $result;
	}
	/**
	 * Determine whether the crawler extension is running and initiated the current request.
	 * @param	Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest $event
	 * @return	boolean
	 */
	protected function isCrawlerExtensionRunning(Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest $event) {
		$result = false;
		if (t3lib_extMgm::isLoaded ( 'crawler' ) && NULL !== $crawlerHeader = $event->getRequest()->getServerVariable ( 'HTTP_X_T3CRAWLER' )) {
			list ( $crawlerQueueId, $crawlerQueueHash ) = explode ( ':', $crawlerHeader );
			$queueRecords = $this->getStorage()->selectQuery ( 'qid,set_id', 'tx_crawler_queue', 'qid=' . intval ( $crawlerQueueId ),'','1' );
			$result = ($queueRecords && $crawlerQueueHash == $this->getCrawlerQueueHash ( $queueRecords [0] ));
		}
		return $result;
	}	
	/**
	 * Determines whether a valid frontend user session is currently active.
	 *
	 * @return	boolean
	 */
	protected function isFrontendUserActive(Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest $event) {
		$frontendUser = $event->getFrontendUser ();
		if (isset ( $frontendUser->user ['uid'] ) && $frontendUser->user ['uid']) {
			return true;
		}
		return false;
	}
	/**
	 * Determines whether a frontend user currently tries to log in.
	 *
	 * @param	Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest $event
	 * @return	boolean
	 * @deprecated Not used anymore
	 */
	protected function isFrontendUserLoggingIn(Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest $event) {
		$loginData = $event->getFrontendUser ()->getLoginFormData ();
		return (isset ( $loginData ['uident'] ) && $loginData ['uident'] && $loginData ['status'] === 'login');
	}
	/**
	 * Determines whether a frontend user currently tries to log out.
	 *
	 * @param	Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest $event
	 * @return	boolean
	 * @deprecated Not used anymore
	 */
	protected function isFrontendUserLoggingOut(Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest $event) {
		$loginData = $event->getFrontendUser ()->getLoginFormData ();
		return (isset ( $loginData ['status'] ) && $loginData ['status'] == 'logout');
	}
	/**
	 * Determines whether the page mailer extension is running and initiated the current request.
	 * @param	Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest $event
	 * @return	boolean
	 */
	protected function isPageMailerExtensionRunning(Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest $event) {
		$result = FALSE;
		if (t3lib_extMgm::isLoaded('aoe_pagemailer') && $event->getRequest()->getServerVariable('HTTP_X_PAGEMAILER')) {
			$result = TRUE;
		}
		return $result;
	}
	/**
	 * Determines whether dirty pages are processed.
	 *
	 * @param	Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest $event
	 * @return	boolean
	 */
	protected function isProcessingDirtyPages(Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest $event) {
		$requestHeader = 'HTTP_' . str_replace('-', '_', Tx_Extracache_Typo3_Hooks_StaticFileCache_DirtyPagesHook::HTTP_Request_Header);
		return ( $event->getRequest()->getServerVariable($requestHeader) !== NULL );
	}
	/**
	 * Determines whether the current request cannot be answered by pre-caching in general.
	 * The behaviour can be configured in common./unprocessibleRequestAction.
	 *
	 * @param	Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest $event
	 * @return	boolean
	 */
	protected function isUnprocessibleRequestAction(Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest $event) {
		$result = false;
		$arguments = $event->getRequest()->getArguments ();
		$unprocessibleRequestArguments = $this->getArgumentRepository()->getArgumentsByType( Tx_Extracache_Domain_Model_Argument::TYPE_unprocessible );

		/* @var $unprocessibleRequestArgument Tx_Extracache_Domain_Model_Argument */
		foreach ( $unprocessibleRequestArguments as $unprocessibleRequestArgument ) {
			$key = $unprocessibleRequestArgument->getName();
			$actions = $unprocessibleRequestArgument->getValue();

			if ($key === '*' && is_array ( $actions )) {
				foreach ( $arguments as $argumentValues ) {
					if (is_array ( $argumentValues )) {
						if (true === $result = $this->getMatchedArguments ( $argumentValues, $actions )) {
							break;
						}
					}
				}
			} elseif (is_bool ( $actions ) && $actions) {
				$result = isset ( $arguments [$key] );
			} elseif (isset ( $arguments [$key] ) && is_array ( $arguments [$key] )=== TRUE && is_array ( $actions ) === TRUE) {
				$result = $this->getMatchedArguments ( $arguments [$key], $actions );
			} elseif(isset ( $arguments [$key] ) && is_array ( $arguments [$key] ) === FALSE && is_array ( $actions ) === FALSE && $arguments [$key] === $actions) {
				$result = true;
			}

			if ($result) {
				break;
			}
		}

		return $result;
	}

	/**
	 * @param array $checkMethods
	 */
	protected function setCheckMethods(array $checkMethods) {
		$this->checkMethods = $checkMethods;
	}

	/**
	 * Gets the matches of the current request arguments concerning actions that shall be searched.
	 *
	 * @param	array		$arguments Current request arguments
	 * @param	array		$actions Action that shall be looked up in arguments
	 * @return	boolean		Whether there have been matches
	 */
	private function getMatchedArguments(array $arguments, array $actions) {
		$result = false;

		$matches = array_intersect_key ( $arguments, $actions );
		if ($matches) {
			foreach ( $matches as $argumentSubKey => $argumentSubValue ) {
				if (is_array ( $actions [$argumentSubKey] ) && in_array ( $argumentSubValue, $actions [$argumentSubKey] ) || $actions [$argumentSubKey] === '*') {
					$result = true;
					break;
				}
			}
		}

		return $result;
	}
	/**
	 * Gets the secure hash of a queued crawler action.
	 *
	 * @param	array		$queueRecord The record of the crawler queue to get the hash for
	 * @return	string		The secure hash of a crawler record
	 */
	private function getCrawlerQueueHash(array $queueRecord) {
		return md5 ( $queueRecord ['qid'] . '|' . $queueRecord ['set_id'] . '|' . $GLOBALS ['TYPO3_CONF_VARS'] ['SYS'] ['encryptionKey'] );
	}
}