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

/**
 * Attention: this class-name must begin with 'tx' and NOT with 'Tx'...otherwise this hook will not work!
 * 
 * @package extracache
 * @subpackage System_StaticCache
 */
class tx_Extracache_System_StaticCache_Dispatcher implements \TYPO3\CMS\Core\SingletonInterface {
	/**
	 * @var	Tx_Extracache_System_StaticCache_AbstractManager
	 */
	protected $cacheManager;
	/**
	 * @var Tx_Extracache_System_Event_Dispatcher
	 */
	protected $eventDispatcher;
	/**
	 * @var	Tx_Extracache_Configuration_ExtensionManager
	 */
	protected $extensionManager;

	/**
	 * Dispatches the content manager and determines whether the current request can be served from cache.
	 *
	 * @return	void
	 */
	public function dispatch() {		
		try {
			// 1. trigger event so that other extensions have the chance to do some stuff before we try to load the requested page from staticCache (even if staticCache is NOT enabled)
			if($this->triggerEventOnStaticCachePreprocess()->isCanceled() === TRUE) {
				return;
			}

			// 2. check if staticCache is enabled
			if ($this->isStaticCacheEnabled () === FALSE) {
				return;
			}

			// 3. try to load the requested page from staticCache
			$this->triggerEventOnStaticCacheContext( TRUE );
 
			// 4. check if request is processible
			if($this->getCacheManager()->isRequestProcessible ()) {
				$this->flush ();
			}
			// 5. don't any longer try to load the requested page from staticCache
			$this->triggerEventOnStaticCacheContext( FALSE );
		} catch ( Exception $e ) {
			$message = 'Exception occured in method dispatch (exceptionClass: '.get_class($e).', exceptionMessage: '.$e->getMessage().')';
			$this->getEventDispatcher()->triggerEvent ( 'onStaticCacheWarning', $this, array ('message' => $message ) );
			if ($this->getExtensionManager()->isDevelopmentContextSet()) {
				throw $e;
			}
		}
	}

	/**
	 * Flushes the cached representation to browser
	 *
	 * @return	void
	 */
	protected function flush() {
		$this->getCacheManager()->logForeignArguments ();
		$content = $this->getCacheManager()->loadCachedRepresentation ();

		if ($content !== false) {
			$this->initializeFrontEnd($content);
			$content = $this->modifyContent($content);
			$this->sendStaticCacheHttpHeader ();
			$this->output( $content );
			$this->halt();
		}
	}

	/**
	 * @return	Tx_Extracache_System_StaticCache_AbstractManager
	 */
	protected function getCacheManager() {
		if($this->cacheManager === NULL) {
			$className = 'Tx_Extracache_System_StaticCache_' . $this->getExtensionManager()->get ( 'enableStaticCacheManager' );
			$typo3DbBackend = GeneralUtility::makeInstance('Tx_Extracache_System_Persistence_Typo3DbBackend');
			$request = GeneralUtility::makeInstance ( 'Tx_Extracache_System_StaticCache_Request' );
			$this->cacheManager = GeneralUtility::makeInstance ( $className, $this->getEventDispatcher(), $this->getExtensionManager(), $typo3DbBackend, $request );
		}
		return $this->cacheManager;
	}
	/**
	 * @return Tx_Extracache_System_ContentProcessor_Chain
	 */
	protected function getContentProcessorChain() {
		return GeneralUtility::makeInstance('Tx_Extracache_System_ContentProcessor_ChainFactory')->getInitialisedChain();
	}
	/**
	 * @return Tx_Extracache_System_Event_Dispatcher
	 */
	protected function getEventDispatcher() {
		if($this->eventDispatcher === NULL) {
			$this->eventDispatcher = GeneralUtility::makeInstance('Tx_Extracache_System_Event_Dispatcher');
		}
		return $this->eventDispatcher;
		
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
	 * Finishes the current request, dispatches shutdown actions and halts.
	 *
	 * @return	void
	 */
	protected function halt() {
		$this->getCacheManager()->getFrontendUserWithInitializedFeGroups ()->storeSessionData ();
		$this->getEventDispatcher()->triggerEvent ( 'onStaticCacheLoaded', $this, array ('message' => 'Cache: Request served by static cache' ) );
		$event = GeneralUtility::makeInstance('Tx_Extracache_System_Event_Events_EventOnStaticCacheResponseHalt');
		$this->getEventDispatcher()->triggerEvent( $event );
		exit ();
	}
	/**
	 * Initializes a light-weight front-end object (TSFE).
	 * The UID and typeNum of the page is determined from the first line of the cached data.
	 *
	 * @param	string		$content The content to be used
	 * @return	void
	 */
	protected function initializeFrontEnd($content) {
		$pageInformation = $this->getCacheManager()->getPageInformationFromCachedRepresentation($content);

		// Re-publish $_GET information if found in cached representation (GET-params/arguments of
        // type 'TYPE_whitelist' will be re-published if they exist when the staticCache was written)
		if (isset($pageInformation['GET'])) {
			$_GET = array_merge($_GET, $pageInformation['GET']);
		}

		/** @var $frontend Tx_Extracache_Typo3_Frontend */
		$GLOBALS['TSFE'] = GeneralUtility::makeInstance(
			'Tx_Extracache_Typo3_Frontend',
			$pageInformation['id'],
			$pageInformation['type'],
			$pageInformation['MP']
		);

			// Restore only really necessary TypoScript config section of that page:
		if (isset($pageInformation['config'])) {
			$GLOBALS['TSFE']->mergeConfiguration($pageInformation['config']);
		}
			// Sets the first rootline page id:
		if (isset($pageInformation['firstRootlineId'])) {
			$GLOBALS['TSFE']->setFirstRootlineId($pageInformation['firstRootlineId']);
		}

		$GLOBALS['TSFE']->finalizeFrontendUser ($this->getCacheManager()->getFrontendUserWithInitializedFeGroups());

		$event = GeneralUtility::makeInstance('Tx_Extracache_System_Event_Events_EventOnInitializeFrontEnd');
		$event->setFrontendUser($GLOBALS['TSFE']->fe_user);
		$this->getEventDispatcher()->triggerEvent( $event );
	}
	/**
	 * Determines whether the static cache is enabled by extension configuration.
	 *
	 * @return	boolean		Whether the static cache is enabled
	 */
	protected function isStaticCacheEnabled() {
		return $this->getExtensionManager()->isStaticCacheEnabled();
	}
	/**
	 * give third-party-extensions the chance to modify the content or to do other stuff (before the content will be send to the client)
	 * 
	 * @param	string $content
	 * @return	string
	 */
	protected function modifyContent($content) {
		// 1. throw event (so third-party-extensions have the chance to do some stuff)
		$event = $this->triggerEventOnStaticCacheResponsePostProcess( $this->getCacheManager()->getCachedRepresentationWithoutPageInformation( $content ) );
		$content = $event->getResponse()->getContent();

		// 2. use contentProcessors (which can be defined in third-party-extensions) to modify the content
		if($this->getExtensionManager()->areContentProcessorsEnabled()) {
			$content = $this->getContentProcessorChain()->process( $content );
		}
		return $content;
	}
	/**
	 * Outputs the content.
	 *
	 * @param string $content
	 * @return void
	 */
	protected function output($content) {
		echo $content;
	}

	/**
	 * Sends a custom HTTP header to indicate the request was processed by the static cache.
	 *
	 * @return	void
	 */
	protected function sendStaticCacheHttpHeader() {
		header ( 'X-StaticCache: 1' );
	}

	/**
	 * @return Tx_Extracache_System_Event_Events_EventOnStaticCachePreprocess
	 */
	private function triggerEventOnStaticCachePreprocess() {
		$event = GeneralUtility::makeInstance('Tx_Extracache_System_Event_Events_EventOnStaticCachePreprocess');
		return $this->getEventDispatcher()->triggerEvent( $event );
	}
	/**
	 * @param boolean $staticCacheContext
	 */
	private function triggerEventOnStaticCacheContext($staticCacheContext) {
		$event = GeneralUtility::makeInstance('Tx_Extracache_System_Event_Events_EventOnStaticCacheContext')->setStaticCacheContext( $staticCacheContext );
		$this->getEventDispatcher()->triggerEvent( $event );
	}
	/**
	 * @param	string $content
	 * @return	Tx_Extracache_System_Event_Events_EventOnStaticCacheResponsePostProcess
	 */
	private function triggerEventOnStaticCacheResponsePostProcess($content) {
		$response = GeneralUtility::makeInstance('Tx_Extracache_System_StaticCache_Response')->setContent( $content );
		$event    = GeneralUtility::makeInstance('Tx_Extracache_System_Event_Events_EventOnStaticCacheResponsePostProcess')->setFrontendUser( $this->getCacheManager()->getFrontendUserWithInitializedFeGroups() )->setResponse( $response );
		return $this->getEventDispatcher()->triggerEvent( $event );
	}
}