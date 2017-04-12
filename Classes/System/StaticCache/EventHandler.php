<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 AOE GmbH <dev@aoe.com>
*  All rights reserved
*
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

require_once(PATH_tx_extracache . 'Classes/Typo3/Hooks/StaticFileCache/DirtyPagesHook.php');

/**
 * @package extracache
 * @subpackage System_StaticCache
 */
class Tx_Extracache_System_StaticCache_EventHandler implements \TYPO3\CMS\Core\SingletonInterface {
	/**
	 * @var Tx_Extracache_Domain_Repository_ArgumentRepository
	 */
	private $argumentRepository;
	/**
	 * @var array
	 */
	private $checkMethods;
	/**
	 * @var Tx_Extracache_Configuration_ExtensionManager
	 */
	private $extensionManager;
	/**
	 * @var Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	private $storage;
	
	/**
	 * constructor
	 */
	public function __construct() {
		$checkMethods = array();

		/**
		 * It's important, that we don't use the staticCache, if a dirty page is in processing!
		 */
		$checkMethods['isProcessingDirtyPages'] = FALSE;

		/**
		 * It's important, that we don't use the staticCache, if FE-user is logging in or out: Some extensions use hooks, which do some
		 * stuff if FE-user is logging in or out. This hooks maybe need the object $GLOBALS['TSFE']->fe_user, which doesn't exist at the
		 * moment (where we create/finalize the fe_user), when the hooks are called!
		 */
		if(FALSE === $this->getExtensionManager()->isCachingDuringLoginAndLogoutEnabled()) {
			$checkMethods['isFrontendUserLoggingIn'] = FALSE;
			$checkMethods['isFrontendUserLoggingOut'] = FALSE;
		}

		/**
		 * if we don't support FE-usergroups, no fe-user is allowed to be logged in
		 */
		if(FALSE === $this->getExtensionManager()->isSupportForFeUsergroupsSet()) {
			$checkMethods['isFrontendUserActive'] = FALSE;
		}

		$checkMethods['isUnprocessibleRequestAction'] = FALSE;
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
			$this->argumentRepository = GeneralUtility::makeInstance ( 'Tx_Extracache_Domain_Repository_ArgumentRepository' );
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
	 * @return Tx_Extracache_Configuration_ExtensionManager
	 */
	protected function getExtensionManager() {
		if($this->extensionManager === NULL) {
			$this->extensionManager = GeneralUtility::makeInstance('Tx_Extracache_Configuration_ExtensionManager');
		}
		return $this->extensionManager;
	}
	/**
	 * @return Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	protected function getStorage() {
		if($this->storage === NULL) {
			$this->storage = GeneralUtility::makeInstance('Tx_Extracache_System_Persistence_Typo3DbBackend');
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
			/* @var $backendUser \TYPO3\CMS\Backend\FrontendBackendUserAuthentication */
			$backendUser = GeneralUtility::makeInstance ( 'TYPO3\\CMS\\Backend\\FrontendBackendUserAuthentication' );
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
		if (ExtensionManagementUtility::isLoaded ( 'crawler' ) && NULL !== $crawlerHeader = $event->getRequest()->getServerVariable ( 'HTTP_X_T3CRAWLER' )) {
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
	 */
	protected function isFrontendUserLoggingOut(Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest $event) {
		$loginData = $event->getFrontendUser ()->getLoginFormData ();
		return (isset ( $loginData ['status'] ) && $loginData ['status'] == 'logout');
	}
	/**
	 * Determines whether dirty pages are processed.
	 *
	 * @param	Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest $event
	 * @return	boolean
	 */
	protected function isProcessingDirtyPages(Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest $event) {
		$requestHeader = 'HTTP_' . str_replace('-', '_', tx_Extracache_Typo3_Hooks_StaticFileCache_DirtyPagesHook::HTTP_Request_Header);
		return ( $event->getRequest()->getServerVariable($requestHeader) !== NULL );
	}
	/**
	 * Determines whether the current request cannot be answered by pre-caching in general.
	 * The behaviour can be configured via creating Tx_Extracache_Domain_Model_Argument-objects with Type Tx_Extracache_Domain_Model_Argument::TYPE_unprocessible
	 *
	 * @param	Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest $event
	 * @return	boolean
	 */
	protected function isUnprocessibleRequestAction(Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest $event) {
		return Tx_Extracache_System_Tools_Request::isUnprocessibleRequest(
			$event->getRequest()->getArguments (),
			$this->getArgumentRepository()->getArgumentsByType( Tx_Extracache_Domain_Model_Argument::TYPE_unprocessible )
		);
	}

	/**
	 * @param array $checkMethods
	 */
	protected function setCheckMethods(array $checkMethods) {
		$this->checkMethods = $checkMethods;
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