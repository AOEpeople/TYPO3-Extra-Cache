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

use TYPO3\CMS\Core\Utility\GeneralUtility;

require_once (PATH_tx_extracache . 'Classes/System/Tools/Uri.php');

/**
 * @package extracache
 * @subpackage System_StaticCache
 */
abstract class Tx_Extracache_System_StaticCache_AbstractManager implements \TYPO3\CMS\Core\SingletonInterface {
	const DATA_PageInformationPrefix = '<!--[page:';
	const DATA_PageInformationSuffix = ']-->';

	/**
	 * @var	Tx_Extracache_Xclass_FrontendUserAuthentication
	 */
	protected $frontendUserWithInitializedFeGroups;
	/**
	 * @var Tx_Extracache_Domain_Repository_ArgumentRepository
	 */
	private $argumentRepository;
	/**
	 * @var string
	 */
	private $cachedRepresentationGroupList;
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
            $content = preg_replace('/^\n/', '', $content); // remove empty line, if exists
		}
		return $content;
	}
	/**
	 * @return TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication
	 */
	public function getFrontendUser() {
        return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Authentication\\FrontendUserAuthentication');
	}
    /**
     * @return Tx_Extracache_Xclass_FrontendUserAuthentication	Frontend user handler
     */
    public function getFrontendUserWithInitializedFeGroups() {
        if (! isset ( $this->frontendUserWithInitializedFeGroups )) {
            $frontendVars = & $GLOBALS ['TYPO3_CONF_VARS'] ['FE'];
            $this->frontendUserWithInitializedFeGroups = $this->getFrontendUser();
            $this->frontendUserWithInitializedFeGroups->lockIP = $frontendVars ['lockIP'];
            $this->frontendUserWithInitializedFeGroups->lockHashKeyWords = $frontendVars ['lockHashKeyWords'];
            $this->frontendUserWithInitializedFeGroups->checkPid = $frontendVars ['checkFeUserPid'];
            $this->frontendUserWithInitializedFeGroups->lifetime = intval ( $frontendVars ['lifetime'] );
            $this->frontendUserWithInitializedFeGroups->start ();
            $this->frontendUserWithInitializedFeGroups->fetchGroupData();
        }
        return $this->frontendUserWithInitializedFeGroups;
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
        /* @var $event Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest */
        $event = GeneralUtility::makeInstance('Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest');
        $event->setFrontendUser( $this->getFrontendUser() );
        $event->setRequest( $this->getRequest() );
        $this->getDispatcher()->triggerEvent( $event );
        /**
         * it's important, that we check if cached representation is available AFTER we have checked if we can respond
         * the request (otherwise a fatal-error can occur if FE-user is logging in or out; for more informations take a
         * look at: Tx_Extracache_System_StaticCache_EventHandler->__construct())!
         */
        if($event->isCanceled() === FALSE && $this->isCachedRepresentationAvailable () === FALSE) {
            $event->cancel();
            $event->setReasonForCancelation( 'Check "isCachedRepresentationAvailable" prevents from using static caching' );
        }

        if ($event->isCanceled()) {
            if (NULL !== $reasonForCancelation = $event->getReasonForCancelation()) {
                $this->getDispatcher()->triggerEvent ( 'onStaticCacheInfo', $this, array ('message' => $reasonForCancelation ) );
            }
            return false;
        }
        return true;
	}

	/**
	 * Loads the content of the cached representation.
	 *
	 * @return	mixed		The content of the cached representation (string)
	 *						or FALSE (boolean) if cached representation doesn't exist OR if cached representation was just deleted after we checked if cached representation exists
	 */
	public function loadCachedRepresentation() {
		$result = FALSE;
		if ($this->isCachedRepresentationAvailable ()) {
			$cacheRepresentation = $this->getCachedRepresentation ();
			$result = @file_get_contents ( $cacheRepresentation );
			if($result !== FALSE) {
				$this->getDispatcher()->triggerEvent ( 'onStaticCacheLoaded', $this, array ('message' => 'Cache representation "' . $cacheRepresentation . '" loaded and contains ' . strlen ( $result ) . ' bytes' ) );
			}
		}
		return $result;
	}
	/**
	 * Checks whether the URI contains arguments that could not be filtered/ignored and logs it.
	 * This can (NOT must) indicate missing RealURL configuration or other problems on generating URLs.
	 *
	 * @return	void
	 */
	public function logForeignArguments() {
		$filteredOriginalUri = Tx_Extracache_System_Tools_Uri::filterUriArguments ( $this->getRequest()->getFileNameWithQuery (), $this->getArgumentRepository()->getArgumentsByType(Tx_Extracache_Domain_Model_Argument::TYPE_ignoreOnCreatingCache) );
		if (strpos ( $filteredOriginalUri, '?' ) !== false) {
			$this->getDispatcher()->triggerEvent ( 'onStaticCacheInfo', $this, array ('message' => 'URI "' . $this->getRequest()->getFileNameWithQuery () . '" contains foreign arguments.' ) );
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
	 * This method is only used in classes, which extends this class.
	 *
	 * @return string
	 */
	protected function getCachedRepresentationGroupList() {
		if (!isset($this->cachedRepresentationGroupList)) {
			$this->cachedRepresentationGroupList = $this->getFrontendUserWithInitializedFeGroups()->getGroupList();
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
		return $this->isCachedRepresentationAccessible($this->getCachedRepresentation());
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