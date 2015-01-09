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

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @package extracache
 */
class Tx_Extracache_Configuration_ConfigurationManager implements \TYPO3\CMS\Core\SingletonInterface {
	/**
	 * @var Tx_Extracache_Domain_Repository_ArgumentRepository
	 */
	private $argumentRepository;
	/**
	 * @var Tx_Extracache_Domain_Repository_CleanerStrategyRepository
	 */
	private $cleanerStrategyRepository;
	/**
	 * @var Tx_Extracache_Domain_Repository_ContentProcessorDefinitionRepository
	 */
	private $contentProcessorDefinitionRepository;
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
		$argument = GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, $type, $value);
        $result = $this->getArgumentValidator()->validate($argument);
        if ($result->hasErrors()) {
            $this->throwException($result->getErrors());
        }
        $this->getArgumentRepository()->addArgument($argument);
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
        $result = $this->getCleanerStrategyValidator()->validate($cleanerStrategy);
        if ($result->hasErrors()) {
            $this->throwException($result->getErrors());
        }
        $this->getCleanerStrategyRepository()->addStrategy($cleanerStrategy);
	}
	/**
	 * @param string $className	className of contentProcessor
	 * @param string $path		path (include name of PHP-file) to contentProcessor (must only be defined, if className and path don't use the synthax of extbase)
	 */
	public function addContentProcessorDefinition($className, $path=NULL) {
		$contentProcessorDefinition = GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_ContentProcessorDefinition', $className, $path);
		$this->getContentProcessorDefinitionRepository()->addContentProcessorDefinition($contentProcessorDefinition);
	}
	/**
	 * @param	string	$key
	 * @param	string	$name		optional, default is ''
	 * @param	integer	$interval	optional, default is 0
	 * @param	boolean	$writeLog	optional, default is FALSE
	 * @throws	RuntimeException
	 */
	public function addEvent($key, $name='', $interval=0, $writeLog=FALSE) {
		$event = $this->createEvent($key, $name, $interval, $writeLog);
        $result = $this->getEventValidator()->validate($event);
        if ($result->hasErrors()) {
            $this->throwException($result->getErrors());
        }
        $this->getEventRepository()->addEvent($event);
	}
	
	/**
	 * @return Tx_Extracache_Domain_Repository_ArgumentRepository
	 */
	public function getArgumentRepository() {
		if($this->argumentRepository === NULL) {
			$this->argumentRepository = GeneralUtility::makeInstance('Tx_Extracache_Domain_Repository_ArgumentRepository');
		}
		return $this->argumentRepository;
	}
	/**
	 * @return Tx_Extracache_Validation_Validator_Argument
	 */
	protected function getArgumentValidator() {
		return GeneralUtility::makeInstance('Tx_Extracache_Validation_Validator_Argument');
	}
	/**
	 * @return Tx_Extracache_Domain_Repository_CleanerStrategyRepository
	 */
	protected function getCleanerStrategyRepository() {
		if($this->cleanerStrategyRepository === NULL) {
			$this->cleanerStrategyRepository = GeneralUtility::makeInstance('Tx_Extracache_Domain_Repository_CleanerStrategyRepository');
		}
		return $this->cleanerStrategyRepository;
	}
	/**
	 * @return Tx_Extracache_Validation_Validator_CleanerStrategy
	 */
	protected function getCleanerStrategyValidator() {
		return GeneralUtility::makeInstance('Tx_Extracache_Validation_Validator_CleanerStrategy');
	}
	/**
	 * @return Tx_Extracache_Domain_Repository_ContentProcessorDefinitionRepository
	 */
	protected function getContentProcessorDefinitionRepository() {
		if($this->contentProcessorDefinitionRepository === NULL) {
			$this->contentProcessorDefinitionRepository = GeneralUtility::makeInstance('Tx_Extracache_Domain_Repository_ContentProcessorDefinitionRepository');
		}
		return $this->contentProcessorDefinitionRepository;
	}
	/**
	 * @return Tx_Extracache_Domain_Repository_EventRepository
	 */
	protected function getEventRepository() {
		if($this->eventRepository === NULL) {
			$this->eventRepository = GeneralUtility::makeInstance('Tx_Extracache_Domain_Repository_EventRepository');
		}
		return $this->eventRepository;
	}
	/**
	 * @return Tx_Extracache_Validation_Validator_Event
	 */
	protected function getEventValidator() {
		return GeneralUtility::makeInstance('Tx_Extracache_Validation_Validator_Event');
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
		return GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_CleanerStrategy', $actions, $childrenMode, $elementsMode, $key, $name);
	}
	/**
	 * @param	string	$key
	 * @param	string	$name
	 * @param	integer	$interval
	 * @param	boolean	$writeLog
	 * @return	Tx_Extracache_Domain_Model_Event
	 */
	private function createEvent($key, $name, $interval, $writeLog) {
		if($name === '') {
			$name = $key;
		}
		return GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_Event', $key, $name, $interval, $writeLog);
	}

    /***
     * @param array $errors
     * @throws	RuntimeException
     */
    private function throwException(array $errors)
    {
        $errorDetails = array();
        foreach($errors as $error) {
            /* @var $error \TYPO3\CMS\Extbase\Error\Error */
            $errorDetails[] = $error->getMessage() . ' [' . $error->getCode() . ']';
        }
        throw new RuntimeException('object is not valid (' . implode(',', $errorDetails) . ')!');
    }
}