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
class Tx_Extracache_Validation_Validator_Event extends Tx_Extbase_Validation_Validator_AbstractValidator {
	/**
	 * @param	Tx_Extracache_Domain_Model_Event $event
	 * @return	boolean
	 */
	public function isValid($event) {
		if($this->getEventRepository()->hasEvent($event->getKey())) {
			$this->addError('event with key ' . $event->getKey() . ' does already exist!', 1289898441);
		}
		if(is_integer( $event->getInterval() ) === FALSE || $event->getInterval() < 0) {
			$this->addError('interval '.$event->getInterval().' is not a positive integer-value!', 1291388576);
		}
		return (count($this->getErrors()) === 0);
	}
	
	/**
	 * @return Tx_Extracache_Domain_Repository_EventRepository
	 */
	protected function getEventRepository() {
		return t3lib_div::makeInstance('Tx_Extracache_Domain_Repository_EventRepository');
	}
}