<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 AOE GmbH <dev@aoe.com>
*  All rights reserved
*
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @package extracache
 */
class Tx_Extracache_Validation_Validator_Event extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator {
	/**
	 * @param	Tx_Extracache_Domain_Model_Event $event
	 * @return	void
	 */
    protected function isValid($event) {
		if($this->getEventRepository()->hasEvent($event->getKey())) {
			$this->addError('event with key ' . $event->getKey() . ' does already exist!', 1289898441);
		}
		if(is_integer( $event->getInterval() ) === FALSE || $event->getInterval() < 0) {
			$this->addError('interval '.$event->getInterval().' is not a positive integer-value!', 1291388576);
		}
	}

	/**
	 * @return Tx_Extracache_Domain_Repository_EventRepository
	 */
	protected function getEventRepository() {
		return GeneralUtility::makeInstance('Tx_Extracache_Domain_Repository_EventRepository');
	}
}