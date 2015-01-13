<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 AOE GmbH <dev@aoe.com>
*  All rights reserved
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * scheduler-task to to release cache deadlocks
 * 
 * @package extracache
 * @subpackage Typo3
 */
class Tx_Extracache_Typo3_SchedulerTaskReleaseCacheDeadlocks extends \TYPO3\CMS\Scheduler\Task\AbstractTask {
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
		$sqlForPrimaryKey1 = "CONCAT(page_id, '|', tx_extracache_grouplist)"; // definition of composite-key (pageId + feGroupIds) for table 'cache_pages'
		$sqlForPrimaryKey2 = "CONCAT(pid, '|', tx_extracache_grouplist)";     // definition of composite-key (pageId + feGroupIds) for table 'tx_ncstaticfilecache_file'
		$whereStmt = sprintf("%s NOT IN (SELECT %s FROM tx_ncstaticfilecache_file) AND tstamp < (UNIX_TIMESTAMP()-%d)", $sqlForPrimaryKey1, $sqlForPrimaryKey2, $this->deleteEntriesOlderThanSeconds);

		if($this->detailLogInfo) {
			$rows = $this->getTypo3DbBackend()->selectQuery('page_id', 'cache_pages', $whereStmt, 'page_id ASC');
			$this->checkDBError();
			if(is_array($rows) && count($rows) > 0) {
				$pageIds = array();
				foreach ($rows as $row) {
					if(FALSE === in_array($row['page_id'], $pageIds)) {
						$pageIds[] = $row['page_id'];
					}
				}
				$logInfo = sprintf($GLOBALS['LANG']->getLL('logmsg_releaseCacheDeadlocks_detail'), count($rows), $this->deleteEntriesOlderThanSeconds, implode(', ', $pageIds));
				$this->writeLogNotice($logInfo);
			}
		}

		$this->getTypo3DbBackend()->deleteQuery('cache_pages', $whereStmt);
		$this->checkDBError();
		if($this->getTypo3DbBackend()->getAffectedRows() > 0) {
			$logInfo = sprintf($GLOBALS['LANG']->getLL('logmsg_releaseCacheDeadlocks'), $this->getTypo3DbBackend()->getAffectedRows(), $this->deleteEntriesOlderThanSeconds);
			$this->writeLogNotice($logInfo);
		}
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
	 * @return Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	protected function getTypo3DbBackend() {
		if($this->typo3DbBackend === NULL) {
			$this->typo3DbBackend = GeneralUtility::makeInstance('Tx_Extracache_System_Persistence_Typo3DbBackend');
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