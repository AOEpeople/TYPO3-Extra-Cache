<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 AOE GmbH <dev@aoe.com>
 *  All rights reserved
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * class to define the additional field 'clientIdForProductCatalogue'
 * @package extracache
 * @subpackage typo3
 */
class Tx_Extracache_Typo3_SchedulerTaskReleaseCacheDeadlocksAdditionalFields implements tx_scheduler_AdditionalFieldProvider {
	/**
	 * Gets additional fields to render in the form to add/edit a task
	 *
	 * @param array $taskInfo						Values of the fields from the add/edit task form
	 * @param tx_scheduler_Task $task				The task object being edited. Null when adding a task!
	 * @param tx_scheduler_Module $schedulerModule	Reference to the scheduler backend module
	 * @return	array A two dimensional array, array('Identifier' => array('fieldId' => array('code' => '', 'label' => '', 'cshKey' => '', 'cshLabel' => ''))
	 */
	public function getAdditionalFields(array &$taskInfo, $task, tx_scheduler_Module $schedulerModule) {
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
	 * @param tx_scheduler_Task $task
	 */
	public function saveAdditionalFields(array $submittedData, tx_scheduler_Task $task) {
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
	 * @param tx_scheduler_Module $parentObject
	 * @return boolean
	 */
	public function validateAdditionalFields(array &$submittedData, tx_scheduler_Module $parentObject) {
		$GLOBALS['LANG']->includeLLFile('EXT:extracache/Resources/Private/Language/locallang.xml');

        if( version_compare(TYPO3_version,'4.6.0','>=') ) {
            $deleteEntriesOlderThanSeconds = t3lib_utility_Math::convertToPositiveInteger ((int) trim($submittedData['deleteEntriesOlderThanSeconds']));
        } else {
            $deleteEntriesOlderThanSeconds = t3lib_div::intval_positive((int) trim($submittedData['deleteEntriesOlderThanSeconds']));
        }

		if (($deleteEntriesOlderThanSeconds >= 0) && ($deleteEntriesOlderThanSeconds<=999999)) {
			$isValid = TRUE;
			$submittedData['deleteEntriesOlderThanSeconds'] = $deleteEntriesOlderThanSeconds;
		} else {
			$isValid = FALSE;
			$parentObject->addMessage($GLOBALS['LANG']->getLL('valerr_schedulerFieldDeleteEntriesOlderThanSeconds'), t3lib_FlashMessage::ERROR);
		}
		return $isValid;
	}
}
