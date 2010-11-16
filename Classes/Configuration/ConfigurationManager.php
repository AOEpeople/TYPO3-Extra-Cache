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
class Tx_Extracache_Configuration_ConfigurationManager implements t3lib_Singleton {
	/**
	 * @var Tx_Extracache_Domain_Repository_ArgumentRepository
	 */
	private $argumentRepository;
	/**
	 * @var Tx_Extracache_Domain_Repository_CleanerStrategyRepository
	 */
	private $cleanerStrategyRepository;
	/**
	 * @var Tx_Extracache_Domain_Repository_EventRepository
	 */
	private $eventRepository;

	/**
	 * @param	string $type
	 * @param	string $name
	 * @param	mixed $value
	 * @throws	RuntimeException
	 */
	public function addArgument($type, $name, $value) {
		$argument  = t3lib_div::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, $type, $value);
		$validator = $this->getArgumentValidator();
		if($validator->isValid($argument)) {
			$this->getArgumentRepository()->addArgument($argument);
		} else {
			throw new RuntimeException( implode(',',$validator->getErrors()) );
		}
	}
	/**
	 * @param	integer $actions
	 * @param	string $childrenMode
	 * @param	string $elementsMode
	 * @param	string $key
	 * @param	string $name
	 * @throws	RuntimeException
	 */
	public function addCleanerStrategy($actions, $childrenMode, $elementsMode, $key, $name='') {
		$cleanerStrategy = $this->createCleanerStrategy($actions, $childrenMode, $elementsMode, $key, $name);
		$validator = $this->getCleanerStrategyValidator();
		if($validator->isValid($cleanerStrategy)) {
			$this->getCleanerStrategyRepository()->addStrategy($cleanerStrategy);
		} else {
			throw new RuntimeException( implode(',',$validator->getErrors()) );
		}
	}
	/**
	 * @param	string $key
	 * @param	string $name
	 * @throws	RuntimeException
	 */
	public function addEvent($key, $name='') {
		$event = $this->createEvent($key, $name);
		$validator = $this->getEventValidator();
		if($validator->isValid($event)) {
			$this->getEventRepository()->addEvent($event);
		} else {
			throw new RuntimeException( implode(',',$validator->getErrors()) );
		}
	}
	
	/**
	 * @return Tx_Extracache_Domain_Repository_ArgumentRepository
	 */
	public function getArgumentRepository() {
		if($this->argumentRepository === NULL) {
			$this->argumentRepository = t3lib_div::makeInstance('Tx_Extracache_Domain_Repository_ArgumentRepository');
		}
		return $this->argumentRepository;
	}
	/**
	 * @return Tx_Extracache_Validation_Validator_Argument
	 */
	protected function getArgumentValidator() {
		return t3lib_div::makeInstance('Tx_Extracache_Validation_Validator_Argument');
	}
	/**
	 * @return Tx_Extracache_Domain_Repository_CleanerStrategyRepository
	 */
	protected function getCleanerStrategyRepository() {
		if($this->cleanerStrategyRepository === NULL) {
			$this->cleanerStrategyRepository = t3lib_div::makeInstance('Tx_Extracache_Domain_Repository_CleanerStrategyRepository');
		}
		return $this->cleanerStrategyRepository;
	}
	/**
	 * @return Tx_Extracache_Validation_Validator_CleanerStrategy
	 */
	protected function getCleanerStrategyValidator() {
		return t3lib_div::makeInstance('Tx_Extracache_Validation_Validator_CleanerStrategy');
	}
	/**
	 * @return Tx_Extracache_Domain_Repository_EventRepository
	 */
	protected function getEventRepository() {
		if($this->eventRepository === NULL) {
			$this->eventRepository = t3lib_div::makeInstance('Tx_Extracache_Domain_Repository_EventRepository');
		}
		return $this->eventRepository;
	}
	/**
	 * @return Tx_Extracache_Validation_Validator_Event
	 */
	protected function getEventValidator() {
		return t3lib_div::makeInstance('Tx_Extracache_Validation_Validator_Event');
	}

	/**
	 * @param	integer $actions
	 * @param	string $childrenMode
	 * @param	string $elementsMode
	 * @param	string $key
	 * @param	string $name
	 * @return	Tx_Extracache_Domain_Model_CleanerStrategy
	 */
	private function createCleanerStrategy($actions, $childrenMode, $elementsMode, $key, $name) {
		if($name === '') {
			$name = $key;
		}
		return t3lib_div::makeInstance('Tx_Extracache_Domain_Model_CleanerStrategy', $actions, $childrenMode, $elementsMode, $key, $name);
	}
	/**
	 * @param	string $key
	 * @param	string $name
	 * @return	Tx_Extracache_Domain_Model_Event
	 */
	private function createEvent($key, $name) {
		if($name === '') {
			$name = $key;
		}
		return t3lib_div::makeInstance('Tx_Extracache_Domain_Model_Event', $key, $name);
	}
}