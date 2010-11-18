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
require_once(PATH_tx_extracache . 'Classes/Typo3/Hooks/FileReferenceModification.php');

/**
 * scheduler-task to clean-up removed files
 * 
 * @package extracache
 * @subpackage Typo3_Hooks
 */
class Tx_Extracache_Typo3_SchedulerTaskCleanUpRemovedFiles extends tx_scheduler_Task {
	/**
	 * @var Tx_Extracache_System_Event_Dispatcher
	 */
	private $eventDispatcher;
	/**
	 * @var Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	private $typo3DbBackend;

	/**
	 * execute the task
	 * @return boolean
	 */
	public function execute() {
		$shellExitCode = TRUE;
		try {
			$this->cleanUpRemovedFiles();
		} catch (Exception $e) {
			$message = 'Exception occured in scheduler-task "cleanUpRemovedFiles" (exceptionClass: '.get_class($e).', exceptionMessage: '.$e->getMessage().')';
			$this->getEventDispatcher()->triggerEvent ( 'onCleanUpRemovedFilesError', $this, array ('message' => $message ) );
			$shellExitCode = FALSE;
		}
		return $shellExitCode;
	}

	/**
	 * clean-up removed files
	 */
	protected function cleanUpRemovedFiles() {
		$rows = $this->getTypo3DbBackend()->selectQuery('*', Tx_Extracache_Typo3_Hooks_FileReferenceModification::TABLE_Queue);
		foreach ($rows as $row) {
			$files = t3lib_div::trimExplode(',', $row['files'], true);
			foreach ($files as $file) {
				if (is_file(PATH_site . $file)) {
					unlink(PATH_site . $file);
				}
			}
			$this->getTypo3DbBackend()->deleteQuery(Tx_Extracache_Typo3_Hooks_FileReferenceModification::TABLE_Queue, 'id=' . $row['id']);
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
}