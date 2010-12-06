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
 * @subpackage System
 */
class Tx_Extracache_System_EventQueue implements t3lib_Singleton {
	const TABLE_Queue = 'tx_extracache_eventqueue';
	const STATUS_WaitForProcessing = 0;
	const STATUS_InProcess = 1;

	/**
	 * @var Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	private $typo3DbBackend;

	/**
	 * add event to queue if event not already exists with status 'STATUS_WaitForProcessing'
	 * 
	 * @param Tx_Extracache_Domain_Model_Event $event
	 */
	public function addEvent(Tx_Extracache_Domain_Model_Event $event) {
		if($this->eventIsAlreadyInQueue($event) === FALSE) {
			$values = array();
			$values['first_called_time'] = $this->getTime();
			$values['event_interval'] = $event->getInterval();
			$values['event_key'] = $event->getKey();
			$values['status'] = self::STATUS_WaitForProcessing;
			$this->getTypo3DbBackend()->insertQuery(self::TABLE_Queue, $values);
		}
	}

	/**
	 * @param string $eventKey
	 */
	public function deleteEventKey($eventKey) {
		$sqlWhere = 'event_key=' . $this->getTypo3DbBackend()->fullQuoteStr($eventKey, self::TABLE_Queue);
		$sqlWhere.= ' AND status=' . self::STATUS_InProcess;
		$this->getTypo3DbBackend()->deleteQuery(self::TABLE_Queue, $sqlWhere);
	}

	/**
	 * get next event-key, which can be processed
	 * 
	 * @return string
	 */
	public function getNextEventKeyForProcessing() {
		$eventKey  = NULL;
		$sqlWhere = '(status=' . self::STATUS_WaitForProcessing . ' OR status='.self::STATUS_InProcess.') AND ('.self::TABLE_Queue.'.first_called_time+'.self::TABLE_Queue.'.event_interval) < ' . $this->getTime();
		$record = $this->getTypo3DbBackend()->selectQuery('event_key', self::TABLE_Queue, $sqlWhere, 'status DESC, first_called_time ASC, event_interval ASC', '1');
		if(count($record) > 0) {
			$eventKey = $record[0]['event_key'];
			$sqlWhere = 'event_key=' . $this->getTypo3DbBackend()->fullQuoteStr($eventKey, self::TABLE_Queue) . ' AND status='.self::STATUS_WaitForProcessing;
			$values = array();
			$values['status'] = self::STATUS_InProcess;
			$this->getTypo3DbBackend()->updateQuery(self::TABLE_Queue, $sqlWhere, $values);
		}
		return $eventKey;
	}

	/**
	 * get time
	 * 
	 * @return integer
	 */
	protected function getTime() {
		return time();
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
	 * check if event with status 'STATUS_WaitForProcessing' is already in queue
	 * 
	 * @param	Tx_Extracache_Domain_Model_Event $event
	 * @return	boolean
	 */
	private function eventIsAlreadyInQueue(Tx_Extracache_Domain_Model_Event $event) {
		$sqlWhere = 'event_key=' . $this->getTypo3DbBackend()->fullQuoteStr($event->getKey(), self::TABLE_Queue);
		$sqlWhere.= ' AND status=' . self::STATUS_WaitForProcessing;
		$records = $this->getTypo3DbBackend()->selectQuery('id', self::TABLE_Queue, $sqlWhere, '', '1');
		return ( count($records) > 0 );
	}
}