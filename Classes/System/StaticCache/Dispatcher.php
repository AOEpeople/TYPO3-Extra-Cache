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
 * @package extracache
 * @subpackage System_StaticCache
 */
class Tx_Extracache_System_StaticCache_Dispatcher implements t3lib_Singleton {
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
			$event = $this->triggerEventOnStaticCacheResponsePostProcess( $content );
			$this->sendStaticCacheHttpHeader ();
			$this->output( $event->getResponse()->getContent() );
			$this->halt();
		}
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

		//@todo: we must process this event in tx_eft_system_staticCache_dispatcher
		// so we can call this php-code in tx_eft_system_staticCache_dispatcher->halt():
		// - tx_eft_shutdown::shutdown ();
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
	private function sendStaticCacheHttpHeader() {
		header ( 'X-StaticCache: 1' );
	}
	/**
	 * @param boolean $staticCacheContext
	 */
	private function triggerEventOnStaticCacheContext($staticCacheContext) {
		//@todo: we must process this event in tx_eft_system_staticCache_dispatcher!
		$event = t3lib_div::makeInstance('Tx_Extracache_System_Event_Events_EventOnStaticCacheContext')->setStaticCacheContext( $staticCacheContext );
		$this->getEventDispatcher()->triggerEvent( $event );
	}
	/**
	 * @param	string $content
	 * @return	Tx_Extracache_System_Event_Events_EventOnStaticCacheResponsePostProcess
	 */
	private function triggerEventOnStaticCacheResponsePostProcess($content) {
		//@todo: we must process this event in tx_eft_system_staticCache_dispatcher
		// so we can call this methods:
		// - tx_eft_system_staticCache_dispatcher->initializeFrontEnd
		// - tx_eft_system_staticCache_dispatcher->processFrontendStartUpHook
		$response = t3lib_div::makeInstance('Tx_Extracache_System_StaticCache_Response')->setContent( $content );
		$event    = t3lib_div::makeInstance('Tx_Extracache_System_Event_Events_EventOnStaticCacheResponsePostProcess')->setResponse( $response );
		return $this->getEventDispatcher()->triggerEvent( $event );
	}
}