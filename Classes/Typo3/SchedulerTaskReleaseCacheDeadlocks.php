<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 AOE media GmbH <dev@aoemedia.de>
*  All rights reserved
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(t3lib_extMgm::extPath('scheduler') . 'class.tx_scheduler_task.php');

/**
 * scheduler-task to to release cache deadlocks
 * 
 * @package extracache
 * @subpackage Typo3
 */
class Tx_Extracache_Typo3_SchedulerTaskReleaseCacheDeadlocks extends tx_scheduler_Task {

	/** Maximum mumber of pageids to show, even when there are more caches released */
	const MAX_LOG_PAGEIDS = 50;

	/** @var Tx_Extracache_System_Event_Dispatcher */
	private $eventDispatcher;

	/** @var Tx_Extracache_System_Persistence_Typo3DbBackend */
	private $typo3DbBackend;

	/**
	 * magic method, called when instance is unserialized
	 */
	public function __wakeup() {
		$GLOBALS['LANG']->includeLLFile('EXT:extracache/Resources/Private/Language/locallang.xml');
	}

	/**
	 * execute the task
	 * @return boolean
	 */
	public function execute() {
		$shellExitCode = TRUE;
		try {
			$this->releaseCacheDeadlocks();
		} catch (Exception $e) {
			$errMsg=sprintf($GLOBALS['LANG']->getLL('errmsg_releaseCacheDeadlocks_generalFailure'), get_class($e), $e->getMessage());
			$this->getEventDispatcher()->triggerEvent('onReleaseCacheDeadlocksError', (object) $this, array ('message' => $errMsg));
			$shellExitCode = FALSE;
		}
		return $shellExitCode;
	}

	/**
	 * release cache deadlocks
	 */
	protected function releaseCacheDeadlocks() {
		$whereStmt='page_id not in (SELECT distinct pid FROM tx_ncstaticfilecache_file) AND tstamp < (UNIX_TIMESTAMP()-'.$this->deleteEntriesOlderThanSeconds.')';

		if($this->detailLogInfo) {
			$rows = $this->getTypo3DbBackend()->selectQuery('page_id', 'cache_pages', $whereStmt);
			$this->checkDBError();
			if(($rows!== NULL) && (count($rows)>0)) {
				$pageIds='';
				foreach ($rows as $row) {
					$pageIds.=$row['page_id'].', ';
				}
				$logInfo=sprintf($GLOBALS['LANG']->getLL('logmsg_releaseCacheDeadlocks_detail'), count($rows), self::MAX_LOG_PAGEIDS, substr($pageIds,0,-2));
				$this->writeLogNotice($logInfo);
			}
		}

		$this->getTypo3DbBackend()->deleteQuery('cache_pages', $whereStmt);
		$this->checkDBError();
		if($this->getTypo3DbBackend()->getAffectedRows()>0) {
			$logInfo=sprintf($GLOBALS['LANG']->getLL('logmsg_releaseCacheDeadlocks'), $this->getTypo3DbBackend()->getAffectedRows());
			$this->writeLogNotice($logInfo);
		}

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
	 * @return Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	protected function getTypo3DbBackend() {
		if($this->typo3DbBackend === NULL) {
			$this->typo3DbBackend = t3lib_div::makeInstance('Tx_Extracache_System_Persistence_Typo3DbBackend');
		}
		return $this->typo3DbBackend;
	}

	/**
	 * @param string $logNotice
	 */
	private function writeLogNotice($logNotice) {
		if($this->logNoticeAsError) {
			$eventName='onReleaseCacheDeadlocksError';
		} else {
			$eventName='onReleaseCacheDeadlocksNotice';
		}
		$this->getEventDispatcher()->triggerEvent($eventName, (object) $this, array ('message' => $logNotice));
	}

	/**
	 * Checks for a database error and throws the appropriate exception if one occurred
	 * @throws Exception
	 */
	private function checkDBError() {
		if($this->getTypo3DbBackend()->getSqlErrorNumber() !== 0) {
			$logInfo=sprintf($GLOBALS['LANG']->getLL('errmsg_releaseCacheDeadlocks_dbFailure'), $this->getTypo3DbBackend()->getSqlErrorNumber(), $this->getTypo3DbBackend()->getSqlErrorText());
			throw new Exception($logInfo);
		}
	}

}