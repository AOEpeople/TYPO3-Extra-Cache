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
class Tx_Extracache_Validation_Validator_Argument extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator {
	/**
	 * @param	Tx_Extracache_Domain_Model_Argument $argument
	 * @return	void
	 */
	protected function isValid($argument) {
		$this->checkName( $argument->getName() );
		$this->checkType( $argument->getType() );
		$this->checkValue( $argument->getValue() );
	}

	/**
	 * @param string $name
	 */
	private function checkName($name) {
		if($name === '' || ($name !== '*' && (boolean) preg_match('/[^a-zA-Z0-9\_\-]/',$name) === TRUE)) {
			$this->addError('name ' . $name . ' is not valid!', 1289897741);
		}
	}
	/**
	 * @param string $type
	 */
	private function checkType($type) {
		if(!in_array($type, Tx_Extracache_Domain_Model_Argument::getSupportedTypes())) {
			$this->addError('type ' . $type . ' is not supported!', 1289897742);
		}
	}
	/**
	 * @param mixed $value
	 */
	private function checkValue($value) {
		if($value !== true && !is_array($value) && !is_string($value) ) {
			$this->addError('value is not valid!', 1289897743);
		}
		if(is_array($value) && count($value) === 0) {
			$this->addError('value is not valid (because value is an empty array)!', 1289897744);
		}
	}
}