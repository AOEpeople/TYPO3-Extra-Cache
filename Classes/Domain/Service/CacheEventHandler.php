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
class Tx_Extracache_Domain_Service_CacheEventHandler implements t3lib_Singleton {
	/**
	 * @var Tx_Extracache_Domain_Repository_CleanerStrategyRepository
	 */
	private $cleanerStrategyRepository;
	/**
	 * @var Tx_Extracache_Domain_Repository_EventRepository
	 */
	private $eventRepository;
	/**
	 * @var Tx_Extracache_Persistence_Typo3DbBackend
	 */
	private $typo3DbBackend;

	/**
	 * @param	string $eventKey
	 * @throws	RuntimeException
	 */
	public function handleEvent($eventKey) {
		if($this->getEventRepository()->hasEvent($eventKey) === FALSE) {
			throw new RuntimeException('event '.$eventKey.' is unknown!');
		}
		$this->processEvent($eventKey);
	}

	/**
	 * @return Tx_Extracache_Domain_Service_CacheCleaner
	 */
	protected function createCacheCleaner() {
		return t3lib_div::makeInstance('Tx_Extracache_Domain_Service_CacheCleaner');
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
	 * @return Tx_Extracache_Domain_Repository_EventRepository
	 */
	protected function getEventRepository() {
		if($this->eventRepository === NULL) {
			$this->eventRepository = t3lib_div::makeInstance('Tx_Extracache_Domain_Repository_EventRepository');
		}
		return $this->eventRepository;
	}
	/**
	 * @return Tx_Extracache_Persistence_Typo3DbBackend
	 */
	protected function getTypo3DbBackend() {
		if($this->typo3DbBackend === NULL) {
			$this->typo3DbBackend = t3lib_div::makeInstance('Tx_Extracache_Persistence_Typo3DbBackend');
		}
		return $this->typo3DbBackend;
	}

	/**
	 * @param	string $eventKey
	 */
	private function processEvent($eventKey) {
		foreach ($this->getTypo3DbBackend()->getPagesWithCacheCleanerStrategyForEvent($eventKey) as $page) {
			try {
				$this->processPage( $page, $eventKey );
			} catch (Exception $e) {
				//@TODO: log exceptions
			}
		}
	}
	/**
	 * @param array $page
	 * @param string $eventKey
	 */
	private function processPage(array $page, $eventKey) {
		$cacheCleaner = $this->createCacheCleaner();

		$strategies = explode(',', $page['tx_extracache_cleanerstrategies']);
		foreach ($strategies as $strategy) {
			if($this->getCleanerStrategyRepository()->hasStrategy($strategy)) {
				$cacheCleaner->addCleanerInstruction($this->getCleanerStrategyRepository()->getStrategy($strategy), (integer) $page['uid']);
			}
		}
		$cacheCleaner->process();
	}
}