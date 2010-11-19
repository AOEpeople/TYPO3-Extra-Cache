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

/**
 * Attention: this class-name must begin with 'tx' and NOT with 'Tx'...otherwise this hook will not work!
 * 
 * @package extracache
 * @subpackage System_StaticCache
 */
class tx_Extracache_System_StaticCache_Dispatcher implements t3lib_Singleton {
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
			if ($this->isStaticCacheEnabled ()) {
				// 1. we try to load the requested page from staticCache
				$this->triggerEventOnStaticCacheContext( TRUE );

				// 2. check if request is processible
				if($this->getCacheManager()->isRequestProcessible ()) {
					$this->flush ();
				}

				// 3. we don't any longer try to load the requested page from staticCache
				$this->triggerEventOnStaticCacheContext( FALSE );
			}
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

			// @todo eft requires the frontend user to be registered:
			// tx_eft_system_IoC_manager::getSingleton ( 'tx_eft_system_registry' )->set ( 'frontendUser', $frontendUser );

			$event = $this->triggerEventOnStaticCacheResponsePostProcess( $content );
			$this->sendStaticCacheHttpHeader ();
			$this->output( $event->getResponse()->getContent() );
			$this->halt();
		}
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

		// Re-publish $_GET information if found in cached representation:
		if (isset($pageInformation['GET'])) {
			$_GET = array_merge($_GET, $pageInformation['GET']);
		}

		/** @var $frontend Tx_Extracache_Typo3_Frontend */
		$frontend = t3lib_div::makeInstance(
			'Tx_Extracache_Typo3_Frontend',
			$pageInformation['id'],
			$pageInformation['type'],
			$pageInformation['MP']
		);

			// Restore only really necessary TypoScript config section of that page:
		if (isset($pageInformation['config'])) {
			$frontend->mergeConfiguration($pageInformation['config']);
		}
			// Sets the page id used to fetch the TypoScript template:
		if (isset($pageInformation['templatePageId'])) {
			$frontend->setTemplatePageId($pageInformation['templatePageId']);
		}
			// Sets the first rootline page id:
		if (isset($pageInformation['firstRootlineId'])) {
			$frontend->setFirstRootlineId($pageInformation['firstRootlineId']);
		}

		$frontend->fe_user = $this->getCacheManager()->getFrontendUser();
		$frontend->finalizeFrontendUser ();

		$GLOBALS['TSFE'] = $frontend;
	}

	/**
	 * @return	Tx_Extracache_System_StaticCache_AbstractManager
	 */
	protected function getCacheManager() {
		if($this->cacheManager === NULL) {
			$className = 'Tx_Extracache_System_StaticCache_' . $this->getExtensionManager()->get ( 'enableStaticCacheManager' );
			$typo3DbBackend = t3lib_div::makeInstance('Tx_Extracache_System_Persistence_Typo3DbBackend');
			$request = t3lib_div::makeInstance ( 'Tx_Extracache_System_StaticCache_Request' );
			$this->cacheManager = t3lib_div::makeInstance ( $className, $this->getEventDispatcher(), $this->getExtensionManager(), $typo3DbBackend, $request );
		}
		return $this->cacheManager;
	}
	/**
	 * @return Tx_Extracache_System_Event_Dispatcher
	 */
	protected function getEventDispatcher() {
		if($this->eventDispatcher === NULL) {
			$this->eventDispatcher = t3lib_div::makeInstance('Tx_Extracache_System_Event_Dispatcher');
		}
		return $this->eventDispatcher;
		
	}
	/**
	 * @return Tx_Extracache_Configuration_ExtensionManager
	 */
	protected function getExtensionManager() {
		if($this->extensionManager === NULL) {
			$this->extensionManager = t3lib_div::makeInstance('Tx_Extracache_Configuration_ExtensionManager');
		}
		return $this->extensionManager;
	}

	/**
	 * Finishes the current request, dispatches shutdown actions and halts.
	 *
	 * @return	void
	 */
	protected function halt() {
		$this->getCacheManager()->getFrontendUser ()->storeSessionData ();
		$this->getEventDispatcher()->triggerEvent ( 'onStaticCacheLoaded', $this, array ('message' => 'Cache: Request served by static cache' ) );
		$event = t3lib_div::makeInstance('Tx_Extracache_System_Event_Events_EventOnStaticCacheResponseHalt');
		$this->getEventDispatcher()->triggerEvent( $event );
		exit ();
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
	 * @param boolean $staticCacheContext
	 */
	private function triggerEventOnStaticCacheContext($staticCacheContext) {
		$event = t3lib_div::makeInstance('Tx_Extracache_System_Event_Events_EventOnStaticCacheContext')->setStaticCacheContext( $staticCacheContext );
		$this->getEventDispatcher()->triggerEvent( $event );
	}
	/**
	 * @param	string $content
	 * @return	Tx_Extracache_System_Event_Events_EventOnStaticCacheResponsePostProcess
	 */
	private function triggerEventOnStaticCacheResponsePostProcess($content) {
		$response = t3lib_div::makeInstance('Tx_Extracache_System_StaticCache_Response')->setContent( $content );
		$event    = t3lib_div::makeInstance('Tx_Extracache_System_Event_Events_EventOnStaticCacheResponsePostProcess')->setFrontendUser( $this->getCacheManager()->getFrontendUser() )->setResponse( $response );
		return $this->getEventDispatcher()->triggerEvent( $event );
	}
}