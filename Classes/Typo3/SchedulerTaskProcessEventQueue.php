<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 AOE media GmbH <dev@aoemedia.de>
*  All rights reserved
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(t3lib_extMgm::extPath('scheduler') . 'class.tx_scheduler_task.php');

/**
 * scheduler-task to process event-queue
 * 
 * @package extracache
 * @subpackage Typo3
 */
class Tx_Extracache_Typo3_SchedulerTaskProcessEventQueue extends tx_scheduler_Task {
	/**
	 * @var Tx_Extracache_Domain_Service_CacheEventHandler
	 */
	private $cacheEventHandler;
	/**
	 * @var Tx_Extracache_System_Event_Dispatcher
	 */
	private $eventDispatcher;
	/**
	 * @var Tx_Extracache_System_EventQueue
	 */
	private $eventQueue;

	/**
	 * execute the task
	 * @return boolean
	 */
	public function execute() {
		$shellExitCode = TRUE;
		try {
			$this->processEventQueue();
		} catch (Exception $e) {
			$message = 'Exception occured in scheduler-task "processEventQueue" (exceptionClass: '.get_class($e).', exceptionMessage: '.$e->getMessage().')';
			$this->getEventDispatcher()->triggerEvent ( 'onProcessEventQueueError', $this, array ('message' => $message ) );
			$shellExitCode = FALSE;
		}
		return $shellExitCode;
	}

	/**
	 * process event-queue
	 */
	protected function processEventQueue() {
		while(NULL !== $eventKey = $this->getEventQueue()->getNextEventKeyForProcessing()) {
			$this->getCacheEventHandler()->processCacheEvent( $eventKey );
			$this->getEventQueue()->deleteEventKey( $eventKey );
		}
	}
	/**
	 * @return Tx_Extracache_Domain_Service_CacheEventHandler
	 */
	protected function getCacheEventHandler() {
		if($this->cacheEventHandler === NULL) {
			$this->cacheEventHandler = t3lib_div::makeInstance('Tx_Extracache_Domain_Service_CacheEventHandler');
		}
		return $this->cacheEventHandler;
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
}