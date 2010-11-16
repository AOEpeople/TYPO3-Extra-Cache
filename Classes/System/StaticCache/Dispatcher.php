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
				// we try to load the requested page from staticCache
				//@todo: we must process this event in tx_eft_system_staticCache_dispatcher!
				$event = t3lib_div::makeInstance('Tx_Extracache_System_Event_Events_EventOnStaticCacheContext');
				$event->setStaticCacheContext( TRUE );
				$this->getEventDispatcher()->triggerEvent( $event );

				$this->initializeCacheManager ();
				$this->getCacheManager()->process ();
				if ($this->getCacheManager()->isProcessed ()) {
					$this->flush ();
				}

				// we don't any longer try to load the requested page from staticCache
				//@todo: we must process this event in tx_eft_system_staticCache_dispatcher!
				$event = t3lib_div::makeInstance('Tx_Extracache_System_Event_Events_EventOnStaticCacheContext');
				$event->setStaticCacheContext( FALSE );
				$this->getEventDispatcher()->triggerEvent( $event );
			}
		} catch ( Exception $e ) {
			$message = 'Exception occured in method dispatch (exceptionClass: '.get_class($e).', exceptionMessage: '.$e->getMessage().')';
			$this->getEventDispatcher()->triggerEvent ( 'onGeneralFailure', $this, array ('message' => $message ) );
			if ($this->getExtensionManager()->get ( 'developmentContext' ) == 1) {
				throw $e;
			}
		}
	}

	/**
	 * Flushes the cached representation to browser if the current request
	 * could be served correctly by the cache manager.
	 *
	 * @return	void
	 */
	protected function flush() {
		if ($this->getCacheManager()->isRequestProcessible ()) {
			$this->getCacheManager()->logForeignArguments ();			
			$content = $this->getCacheManager()->loadCachedRepresentation ();
			
			if ($content !== false) {
				//@todo: we must process this event in tx_eft_system_staticCache_dispatcher
				// so we can call this methods:
				// - tx_eft_system_staticCache_dispatcher->initializeFrontEnd
				// - tx_eft_system_staticCache_dispatcher->processFrontendStartUpHook
				/** @var $response Tx_Extracache_System_StaticCache_Response */
				$response = t3lib_div::makeInstance('Tx_Extracache_System_StaticCache_Response');
				$response->setContent( $content );
				$event = t3lib_div::makeInstance('Tx_Extracache_System_Event_Events_EventOnStaticCacheResponsePostProcess');
				$event->setResponse( $response );
				$this->getEventDispatcher()->triggerEvent( $event );
				
				$this->sendStaticCacheHttpHeader ();
				
				$this->output($event->getResponse()->getContent());
				$this->halt();
			}
		}
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
	 * @return	Tx_Extracache_System_StaticCache_AbstractManager
	 */
	protected function getCacheManager() {
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

		$this->getEventDispatcher()->triggerEvent ( 'onStaticCacheLoaded', $this, array ('msg' => 'Cache: Request served by static cache' ) );

		//@todo: we must process this event in tx_eft_system_staticCache_dispatcher
		// so we can call this php-code in tx_eft_system_staticCache_dispatcher->halt():
		// - tx_eft_shutdown::shutdown ();
		$this->getEventDispatcher()->triggerEvent ( 'onStaticCacheResponseHalt', $this, array ('msg' => 'Cache: Request served by static cache' ) );
		exit ();
	}

	/**
	 * Initializes cacheManager-object
	 *
	 * @return	void
	 */
	protected function initializeCacheManager() {
		$className = 'Tx_Extracache_System_StaticCache_' . $this->getExtensionManager()->get ( 'enableStaticCacheManager' );
		$typo3DbBackend = t3lib_div::makeInstance('Tx_Extracache_System_Persistence_Typo3DbBackend');
		$request = t3lib_div::makeInstance ( 'Tx_Extracache_System_StaticCache_Request' );
		$this->cacheManager = t3lib_div::makeInstance ( $className, $this->getEventDispatcher(), $this->getExtensionManager(), $typo3DbBackend, $request );
	}
	/**
	 * Determines whether the static cache is enabled by extension configuration.
	 *
	 * @return	boolean		Whether the static cache is enabled
	 */
	protected function isStaticCacheEnabled() {
		return ( bool ) $this->getExtensionManager()->get ( 'enableStaticCacheManager' );
	}
	
	/**
	 * Sends a custom HTTP header to indicate the request was processed by the static cache.
	 *
	 * @return	void
	 */
	private function sendStaticCacheHttpHeader() {
		header ( 'X-StaticCache: 1' );
	}
}