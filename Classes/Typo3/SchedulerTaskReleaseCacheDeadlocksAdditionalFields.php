<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 AOE GmbH <dev@aoe.com>
 *  All rights reserved
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * class to define the additional field 'clientIdForProductCatalogue'
 * @package extracache
 * @subpackage typo3
 */
class Tx_Extracache_Typo3_SchedulerTaskReleaseCacheDeadlocksAdditionalFields implements \TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface {
	/**
	 * Gets additional fields to render in the form to add/edit a task
	 *
	 * @param array $taskInfo						Values of the fields from the add/edit task form
	 * @param tx_scheduler_Task $task				The task object being edited. Null when adding a task!
	 * @param \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule	Reference to the scheduler backend module
	 * @return	array A two dimensional array, array('Identifier' => array('fieldId' => array('code' => '', 'label' => '', 'cshKey' => '', 'cshLabel' => ''))
	 */
	public function getAdditionalFields(array &$taskInfo, $task, \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule) {
			$GLOBALS['LANG']->includeLLFile('EXT:extracache/Resources/Private/Language/locallang.xml');

		if (empty( $taskInfo['deleteEntriesOlderThanSeconds'])) {
			if ($schedulerModule->CMD == 'add') {
				$taskInfo['deleteEntriesOlderThanSeconds'] = 1800;
			} elseif ($schedulerModule->CMD == 'edit') {
				$taskInfo['deleteEntriesOlderThanSeconds'] = $task->deleteEntriesOlderThanSeconds;
			} else {
				$taskInfo['deleteEntriesOlderThanSeconds'] = '';
			}
		}

		if (empty($taskInfo['detailLogInfo'])) {
			if ($schedulerModule->CMD == 'add') {
				$taskInfo['detailLogInfo'] = 1;
			} else {
				$taskInfo['detailLogInfo'] = $task->detailLogInfo;
			}
		}

		if (empty($taskInfo['logNoticeAsError'])) {
			if ($schedulerModule->CMD == 'add') {
				$taskInfo['logNoticeAsError'] = 1;
			} else {
				$taskInfo['logNoticeAsError'] = $task->logNoticeAsError;
			}
		}

		// Write the code for the fields
		$additionalFields = array();
		$fieldID = 'task_deleteEntriesOlderThanSeconds';
		$fieldCode = '<input type="number" name="tx_scheduler[deleteEntriesOlderThanSeconds]" id="' . $fieldID . '" value="' . $taskInfo['deleteEntriesOlderThanSeconds'] . '" size="6" />';
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => $GLOBALS['LANG']->getLL('label_schedulerFieldDeleteEntriesOlderThanSeconds')
		);

		$fieldID = 'task_detailLogInfo';
		$fieldCode = '<input type="checkbox"  name="tx_scheduler[detailLogInfo]" id="' . $fieldID . '" '.(htmlspecialchars($taskInfo['detailLogInfo']) ? 'checked="checked"' : '') . ' />';
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => $GLOBALS['LANG']->getLL('label_schedulerFieldDetailLogInfo')
		);

		$fieldID = 'task_logNoticeAsError';
		$fieldCode = '<input type="checkbox"  name="tx_scheduler[logNoticeAsError]" id="' . $fieldID . '" '.(htmlspecialchars($taskInfo['logNoticeAsError']) ? 'checked="checked"' : '') . ' />';
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => $GLOBALS['LANG']->getLL('label_schedulerFieldLogNoticeAsError')
		);

		return $additionalFields;
	}
	/**
	 * @param array $submittedData
	 * @param \TYPO3\CMS\Scheduler\Task\AbstractTask $task
	 */
	public function saveAdditionalFields(array $submittedData, \TYPO3\CMS\Scheduler\Task\AbstractTask $task) {
		$task->deleteEntriesOlderThanSeconds = (int) $submittedData['deleteEntriesOlderThanSeconds'];

		if(!empty($submittedData['detailLogInfo']) && $submittedData['detailLogInfo']=='on') {
			$task->detailLogInfo = 1;
		} else {
			$task->detailLogInfo = 0;
		}

		if(!empty($submittedData['logNoticeAsError']) && $submittedData['logNoticeAsError']=='on') {
			$task->logNoticeAsError= 1;
		} else {
			$task->logNoticeAsError= 0;
		}
	}
	/**
	 * @param array &$submittedData
	 * @param \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $parentObject
	 * @return boolean
	 */
	public function validateAdditionalFields(array &$submittedData, \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $parentObject) {
		$GLOBALS['LANG']->includeLLFile('EXT:extracache/Resources/Private/Language/locallang.xml');

        $deleteEntriesOlderThanSeconds = MathUtility::convertToPositiveInteger ((int) trim($submittedData['deleteEntriesOlderThanSeconds']));
		if (($deleteEntriesOlderThanSeconds >= 0) && ($deleteEntriesOlderThanSeconds<=999999)) {
			$isValid = TRUE;
			$submittedData['deleteEntriesOlderThanSeconds'] = $deleteEntriesOlderThanSeconds;
		} else {
			$isValid = FALSE;
			$parentObject->addMessage($GLOBALS['LANG']->getLL('valerr_schedulerFieldDeleteEntriesOlderThanSeconds'), \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
		}
		return $isValid;
	}
}
