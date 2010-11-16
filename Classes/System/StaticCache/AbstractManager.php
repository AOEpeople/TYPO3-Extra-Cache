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

require_once (t3lib_extMgm::extPath ( 'extracache' ) . 'Classes/System/Tools/Uri.php');

/**
 * @package extracache
 * @subpackage System_StaticCache
 */
abstract class Tx_Extracache_System_StaticCache_AbstractManager implements t3lib_Singleton {
	const DATA_PageInformationPrefix = '<!--[page:';
	const DATA_PageInformationSuffix = ']-->';

	/**
	 * @var string
	 */
	protected $cachedRepresentationGroupList;
	/**
	 * @var	tslib_feUserAuth
	 */
	protected $frontendUser;
	/**
	 * @var boolean
	 */
	protected $isRequestProcessible;
	/**
	 * @var Tx_Extracache_Domain_Repository_ArgumentRepository
	 */
	private $argumentRepository;
	/**
	 * @var Tx_Extracache_System_Event_Dispatcher
	 */
	private $dispatcher;
	/**
	 * @var	Tx_Extracache_Configuration_ExtensionManager
	 */
	private $extensionManager;
	/**
	 * @var Tx_Extracache_System_StaticCache_Request
	 */
	private $request;
	/**
	 * @var Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	private $storage;

	/**
	 * Constructs an instance of this class.
	 * @param Tx_Extracache_System_Event_Dispatcher $dispatcher 
	 * @param Tx_Extracache_Configuration_ExtensionManager $extensionManager
	 * @param Tx_Extracache_System_Persistence_Typo3DbBackend $storage
	 * @param Tx_Extracache_System_StaticCache_Request $request
	 */
	public function __construct(Tx_Extracache_System_Event_Dispatcher $dispatcher, Tx_Extracache_Configuration_ExtensionManager $extensionManager, Tx_Extracache_System_Persistence_Typo3DbBackend $storage, Tx_Extracache_System_StaticCache_Request $request) {
		$this->setDispatcher($dispatcher);
		$this->setExtensionManager($extensionManager);
		$this->setRequest($request);
		$this->setStorage($storage);
	}
	
	/**
	 * Removes the page information from the cached representation.
	 *
	 * @param	string		$content The content of the cached representation
	 * @return	string		The content without the page information
	 */
	public function getCachedRepresentationWithoutPageInformation($content) {
		$startPosition = strpos ( $content, self::DATA_PageInformationPrefix );

		if ($startPosition === 0) {
			$endPosition = strpos ( $content, self::DATA_PageInformationSuffix );
			$stripPosition = $endPosition + strlen ( self::DATA_PageInformationSuffix );
			$content = substr ( $content, $stripPosition );
		}

		return $content;
	}
	/**
	 * Gets a frontend user handler.
	 *
	 * @return	tslib_feUserAuth	Frontend user handler
	 */
	public function getFrontendUser() {
		if (! isset ( $this->frontendUser )) {
			$this->frontendUser = t3lib_div::makeInstance ( 'tslib_feUserAuth' );
		}
		return $this->frontendUser;
	}
	/**
	 * Gets page information from the cached representation like e.g. the page UID.
	 *
	 * @param	string		$content The content of the cached representation
	 * @return	array		The information of the cached page
	 */
	public function getPageInformationFromCachedRepresentation($content) {
		$pageInformation = array ();

		$startPosition = strpos ( $content, self::DATA_PageInformationPrefix );

		if ($startPosition === 0) {
			$prefixLength = strlen ( self::DATA_PageInformationPrefix );
			$endPosition = strpos ( $content, self::DATA_PageInformationSuffix );
			$pageInformation = unserialize ( substr ( $content, $prefixLength, $endPosition - $prefixLength ) );
		}

		return $pageInformation;
	}

	/**
	 * Determines whether the current request can be processed with this pre-rendering cache.
	 *
	 * @return	boolean		Whether the current request can be processed
	 */
	public function isRequestProcessible() {
		if (! isset ( $this->isRequestProcessible )) {
			/* @var $event Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest */
			$event = t3lib_div::makeInstance('Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest')->setFrontendUser( $this->getFrontendUser() )->setRequest( $this->getRequest() );
			if( $this->isCachedRepresentationAvailable () === FALSE) {
				$event->cancel();
				$event->setReasonForCancelation( 'Check "isCachedRepresentationAvailable" prevents from using static caching' );
			} else {
				$this->getDispatcher()->triggerEvent( $event );
			}
			$this->isRequestProcessible = ($event->isCanceled() === FALSE);

			if($event->isCanceled() && NULL !== $reasonForCancelation = $event->getReasonForCancelation() ) {
				$this->getDispatcher()->triggerEvent ( 'onStaticCacheWarning', $this, array ('msg' => $reasonForCancelation ) );
			}
		}
		return $this->isRequestProcessible;
	}

	/**
	 * Loads the content the cached representation.
	 *
	 * @return	mixed		The content of the cached representation (string)
	 *						or false (boolean) if something went wrong
	 */
	public function loadCachedRepresentation() {
		$result = false;
		if ($this->isCachedRepresentationAvailable ()) {
			$cacheRepresentation = $this->getCachedRepresentation ();
			$result = file_get_contents ( $cacheRepresentation );
			$this->getDispatcher()->triggerEvent ( 'onStaticCacheLoaded', $this, array ('msg' => 'Cache representation "' . $cacheRepresentation . '" loaded and contains ' . strlen ( $result ) . ' bytes' ) );
		}
		return $result;
	}
	/**
	 * Checks whether the URI contains arguments that could not be filtered/ignored and logs it.
	 * This idicates missing RealURL configuration or other problems on generating URLs.
	 *
	 * @return	void
	 */
	public function logForeignArguments() {
		$filteredOriginalUri = Tx_Extracache_System_Tools_Uri::filterUriArguments ( $this->getRequest()->getFileNameWithQuery (), $this->getArgumentRepository()->getArgumentsByType(Tx_Extracache_Domain_Model_Argument::TYPE_ignoreOnCreatingCache) );
		if (strpos ( $filteredOriginalUri, '?' ) !== false) {
			$this->getDispatcher()->triggerEvent ( 'onStaticCacheWarning', $this, array ('msg' => 'URI "' . $this->getRequest()->getFileNameWithQuery () . '" contains foreign arguments.' ) );
		}
	}
	/**
	 * Sets the configuration service object to be used to fetch settings from.
	 *
	 * @param	tx_eft_typo3_eftConfigurationService	$configurationService The configuration service object
	 * @return	void
	 */
	public function setConfigurationService(tx_eft_typo3_eftConfigurationService $configurationService) {
		$this->configurationService = $configurationService;
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
	 * @return	string	The cache folder
	 */
	abstract protected function getCachedFolder();
	/**
	 * Gets the cached representation of the current request.
	 *
	 * @return	string		The cached representation of the current request
	 */
	abstract protected function getCachedRepresentation();
	/**
	 * Gets the frontend user group list that IS USED TO GET THE CACHED REPRESENTATION.
	 *
	 * @return string
	 */
	protected function getCachedRepresentationGroupList() {
		if (!isset($this->cachedRepresentationGroupList)) {
			$this->cachedRepresentationGroupList = $this->getFrontendUserGroupList();
		}
		return $this->cachedRepresentationGroupList;
	}
	/**
	 * @return Tx_Extracache_System_Event_Dispatcher
	 */
	protected function getDispatcher() {
		return $this->dispatcher;
	}
	/**
	 * @return Tx_Extracache_Configuration_ExtensionManager
	 */
	protected function getExtensionManager() {
		return $this->extensionManager;
	}
	/**
	 * Gets the frontend user group list.
	 *
	 * @return string
	 */
	protected function getFrontendUserGroupList() {
		return $this->initializeFrontendUser()->getGroupList();
	}
	/**
	 * @return Tx_Extracache_System_StaticCache_Request
	 */
	protected function getRequest() {
		return $this->request;
	}
	/**
	 * @return Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	protected function getStorage() {
		return $this->storage;
	}

	/**
	 * Initializes a frontend user.
	 *
	 * @return tslib_feUserAuth
	 */
	protected function initializeFrontendUser() {
		$frontendUser = $this->getFrontendUser ();

		if ($frontendUser->isInitialized() !== TRUE) {
			$frontendVars = & $GLOBALS ['TYPO3_CONF_VARS'] ['FE'];
			$frontendUser->lockIP = $frontendVars ['lockIP'];
			$frontendUser->lockHashKeyWords = $frontendVars ['lockHashKeyWords'];
			$frontendUser->checkPid = $frontendVars ['checkFeUserPid'];
			$frontendUser->lifetime = intval ( $frontendVars ['lifetime'] );
			$frontendUser->checkPid_value = $this->getStorage()->cleanIntList ( $this->getRequest()->getArgument ( 'pid' ) );
			$frontendUser->start ();
			$frontendUser->fetchGroupData();
		}

		return $frontendUser;
	}
	/**
	 * Determines whether a cached representation is accessible.
	 *
	 * @param string $cachedRepresentation
	 * @return boolean
	 */
	protected function isCachedRepresentationAccessible($cachedRepresentation) {
		return (is_file($cachedRepresentation) && is_readable($cachedRepresentation));
	}
	/**
	 * Determines whether a cached representation of the current process is available.
	 *
	 * @return	boolean
	 */
	protected function isCachedRepresentationAvailable() {
		$result = $this->isCachedRepresentationAccessible($this->getCachedRepresentation());

			// Tries to use the anonymous representation (if available):
			// @todo BUFFALO_3-0: Reactivate anonymous page delivery
		/*
			if ($result === FALSE && $this->cachedRepresentationGroupList !== '0,-1') {
				$this->cachedRepresentationGroupList = '0,-1';
				$cachedRepresentation = $this->getCachedRepresentation();
				if ($this->isCachedRepresentationAccessible($cachedRepresentation)) {
					$pageInformation = $this->getPageInformationFromCachedRepresentation(file_get_contents($cachedRepresentation));
					$result = (isset($pageInformation['isAnonymous']) && $pageInformation['isAnonymous']);
				}
			}
		*/

		return $result;
	}

	/**
	 * @param Tx_Extracache_System_Event_Dispatcher $dispatcher
	 */
	protected function setDispatcher(Tx_Extracache_System_Event_Dispatcher $dispatcher) {
		$this->dispatcher = $dispatcher;
	}
	/**
	 * @param Tx_Extracache_Configuration_ExtensionManager $extensionManager
	 */
	protected function setExtensionManager(Tx_Extracache_Configuration_ExtensionManager $extensionManager) {
		$this->extensionManager = $extensionManager;
	}
	/**
	 * @param Tx_Extracache_System_StaticCache_Request $request
	 */
	protected function setRequest(Tx_Extracache_System_StaticCache_Request $request) {
		$this->request = $request;
	}
	/**
	 * @param Tx_Extracache_System_Persistence_Typo3DbBackend $storage
	 */
	protected function setStorage(Tx_Extracache_System_Persistence_Typo3DbBackend $storage) {
		$this->storage = $storage;
	}
}