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
 */
class Tx_Extracache_Domain_Service_CacheEventHandler implements t3lib_Singleton {
	/**
	 * @var Tx_Extracache_Domain_Repository_CleanerStrategyRepository
	 */
	private $cleanerStrategyRepository;
	/**
	 * @var Tx_Extracache_System_Event_Dispatcher
	 */
	private $eventDispatcher;
	/**
	 * @var Tx_Extracache_System_EventQueue
	 */
	private $eventQueue;
	/**
	 * @var Tx_Extracache_Domain_Repository_EventRepository
	 */
	private $eventRepository;
	/**
	 * @var Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	private $typo3DbBackend;

	/**
	 * @param	Tx_Extracache_System_Event_Events_EventOnProcessCacheEvent $event
	 */
	public function handleEventOnProcessCacheEvent(Tx_Extracache_System_Event_Events_EventOnProcessCacheEvent $event) {
		$cacheEvent = $this->getCacheEvent( $event );
		if($cacheEvent->getInterval() > 0) {
			$this->getEventQueue()->addEvent( $cacheEvent );
		} else {
			$this->processCacheEvent( $cacheEvent );
		}
	}
	/**
	 * @param Tx_Extracache_Domain_Model_Event $event
	 */
	public function processCacheEvent(Tx_Extracache_Domain_Model_Event $event) {
		$eventLog = $this->createEventLog( $event );
		$message = 'start event "onProcessCacheEvent" with cacheEvent "'.$event->getKey().'"';
		$this->getEventDispatcher()->triggerEvent ( 'onProcessCacheEventInfo', $this, array ('message' => $message ) );

		foreach ($this->getTypo3DbBackend()->getPagesWithCacheCleanerStrategyForEvent( $event->getKey() ) as $page) {
			try {
				$eventLog->addInfo( $this->createInfo('process cleanerInstructions on page \''.$page['title'].'\' [id: '.$page['uid'].']', Tx_Extracache_Domain_Model_Info::TYPE_notice) );
				$this->processPageWithCacheEvent( $page );
			} catch (Exception $e) {
				$eventLog->addInfo( $this->createInfo('exception occurred: '.$e->getMessage(), Tx_Extracache_Domain_Model_Info::TYPE_exception) );
				$message = 'Exception occurred at event "onProcessCacheEvent" while processing page "'.$page['title'].'" [id:'.$page['uid'].'] with cacheEvent "'.$event->getKey().'": ' . $e->getMessage().' / '.$e->getTraceAsString();
				$this->getEventDispatcher()->triggerEvent ( 'onProcessCacheEventError', $this, array ('message' => $message ) );
			}
		}

		$eventLog->setStopTime();
		if($event->getWriteLog()) {
			$this->getTypo3DbBackend()->writeEventLog( $eventLog );
		}
	}

	/**
	 * @return Tx_Extracache_Domain_Service_CacheCleaner
	 */
	protected function createCacheCleaner() {
		return t3lib_div::makeInstance('Tx_Extracache_Domain_Service_CacheCleaner');
	}
	/**
	 * @return Tx_Extracache_Domain_Repository_CleanerStrategyRepository
	 */
	protected function getCleanerStrategyRepository() {
		if($this->cleanerStrategyRepository === NULL) {
			$this->cleanerStrategyRepository = t3lib_div::makeInstance('Tx_Extracache_Domain_Repository_CleanerStrategyRepository');
		}
		return $this->cleanerStrategyRepository;
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
	 * @return Tx_Extracache_System_EventQueue
	 */
	protected function getEventQueue() {
		if($this->eventQueue === NULL) {
			$this->eventQueue = t3lib_div::makeInstance('Tx_Extracache_System_EventQueue');
		}
		return $this->eventQueue;
	}
	/**
	 * @return Tx_Extracache_Domain_Repository_EventRepository
	 */
	protected function getEventRepository() {
		if($this->eventRepository === NULL) {
			$this->eventRepository = t3lib_div::makeInstance('Tx_Extracache_Domain_Repository_EventRepository');
		}
		return $this->eventRepository;
	}
	/**
	 * @return Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	protected function getTypo3DbBackend() {
		if($this->typo3DbBackend === NULL) {
			$this->typo3DbBackend = t3lib_div::makeInstance('Tx_Extracache_System_Persistence_Typo3DbBackend');
		}
		return $this->typo3DbBackend;
	}

	/**
	 * @param	Tx_Extracache_Domain_Model_Event $event
	 * @return	Tx_Extracache_Domain_Model_EventLog
	 */
	private function createEventLog(Tx_Extracache_Domain_Model_Event $event) {
		return t3lib_div::makeInstance('Tx_Extracache_Domain_Model_EventLog', $event);
	}
	/**
	 * @param	string $title
	 * @param	string $type
	 * @return	Tx_Extracache_Domain_Model_Info
	 */
	private function createInfo($title, $type) {
		return t3lib_div::makeInstance('Tx_Extracache_Domain_Model_Info', $title, $type);
	}

	/**
	 * @param	Tx_Extracache_System_Event_Events_EventOnProcessCacheEvent $event
	 * @return	Tx_Extracache_Domain_Model_Event
	 * @throws	RuntimeException
	 */
	private function getCacheEvent(Tx_Extracache_System_Event_Events_EventOnProcessCacheEvent $event) {
		$eventKey = $event->getCacheEvent();
		if($this->getEventRepository()->hasEvent($eventKey) === FALSE) {
			throw new RuntimeException('event '.$eventKey.' is unknown!');
		}
		return $this->getEventRepository()->getEvent( $eventKey );
	}
	/**
	 * @param array $page
	 */
	private function processPageWithCacheEvent(array $page) {
		$cacheCleaner = $this->createCacheCleaner();

		$strategies = explode(',', $page['tx_extracache_cleanerstrategies']);
		foreach ($strategies as $strategy) {
			if($this->getCleanerStrategyRepository()->hasStrategy($strategy)) {
				$cacheCleaner->addCleanerInstruction($this->getCleanerStrategyRepository()->getStrategy($strategy), (integer) $page['uid']);
			}
		}
		$cacheCleaner->process();
	}
}