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
class Tx_Extracache_Validation_Validator_CleanerStrategy extends Tx_Extbase_Validation_Validator_AbstractValidator {
	/**
	 * @param	Tx_Extracache_Domain_Model_CleanerStrategy $cleanerStrategy
	 * @return	boolean
	 */
	public function isValid($cleanerStrategy) {
		if($this->getCleanerStrategyRepository()->hasStrategy($cleanerStrategy->getKey())) {
			$this->addError('cleanerStrategy with key '.$cleanerStrategy->getKey().' does already exist!');
		}
		$this->childrenModeIsValid( $cleanerStrategy->getChildrenMode() );
		$this->elementModeIsValid( $cleanerStrategy->getElementsMode() );
		$this->actionsAreValid( $cleanerStrategy->getActions() );
		return (count($this->getErrors()) === 0);
	}
	
	/**
	 * @return Tx_Extracache_Domain_Repository_CleanerStrategyRepository
	 */
	protected function getCleanerStrategyRepository() {
		return t3lib_div::makeInstance('Tx_Extracache_Domain_Repository_CleanerStrategyRepository');
	}

	/**
	 * @param integer $actions
	 */
	private function actionsAreValid($actions) {
		$actionsContainActionNone = $actions & Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_None;
		$actionsContainActionStaticUpdate = $actions & Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_StaticUpdate;
		$actionsContainActionStaticClear = $actions & Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_StaticClear;
		$actionsContainActionStaticDirty = $actions & Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_StaticDirty;
		$actionsContainActionTYPO3Clear = $actions & Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_TYPO3Clear;
		if(
			(boolean) $actionsContainActionNone === FALSE &&
			(boolean) $actionsContainActionStaticUpdate === FALSE &&
			(boolean) $actionsContainActionStaticClear === FALSE &&
			(boolean) $actionsContainActionStaticDirty === FALSE &&
			(boolean) $actionsContainActionTYPO3Clear === FALSE
		) {
			$this->addError('actions '.$actions.' does not contain a valid action!');
		}
	}
	/**
	 * @param string $childrenMode
	 */
	private function childrenModeIsValid($childrenMode) {
		if(!in_array($childrenMode, Tx_Extracache_Domain_Model_CleanerStrategy::getSupportedChildModes())) {
			$this->addError('childrenMode '.$childrenMode.' is not supported!');
		}
	}
	/**
	 * @param string $elementMode
	 */
	private function elementModeIsValid($elementMode) {
		if(!in_array($elementMode, Tx_Extracache_Domain_Model_CleanerStrategy::getSupportedElementModes())) {
			$this->addError('elementMode '.$elementMode.' is not supported!');
		}
	}
}