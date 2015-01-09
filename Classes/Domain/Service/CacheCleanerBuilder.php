<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 AOE GmbH <dev@aoe.com>
*  All rights reserved
*
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @package extracache
 */
class Tx_Extracache_Domain_Service_CacheCleanerBuilder {
	/**
	 * @var Tx_Extracache_Domain_Repository_CleanerStrategyRepository
	 */
	private $cleanerStrategyRepository;

	/**
	 * @param array $page
	 * @return Tx_Extracache_Domain_Service_CacheCleaner
	 */
	public function buildCacheCleanerForPage(array $page) {
		$usedStrategies = array();
		$pageStrategies = explode(',', $page['tx_extracache_cleanerstrategies']);
		foreach ($pageStrategies as $strategy) {
			if($this->getCleanerStrategyRepository()->hasStrategy($strategy)) {
				$usedStrategies[] = $this->getCleanerStrategyRepository()->getStrategy( $strategy );
			}
		}
		return $this->buildCacheCleanerForPageByStrategies($usedStrategies, (integer) $page['uid']);
	}
	/**
	 * @param array $strategies array with objects of type Tx_Extracache_Domain_Model_CleanerStrategy
	 * @param integer $pageId
	 * @return Tx_Extracache_Domain_Service_CacheCleaner
	 */
	public function buildCacheCleanerForPageByStrategies(array $strategies, $pageId) {
		$cacheCleaner = $this->createCacheCleaner();
		foreach ($strategies as $strategy) {
			/* @var $strategy Tx_Extracache_Domain_Model_CleanerStrategy */			
			$cacheCleaner->addCleanerInstruction( $strategy, $pageId );
		}
		return $cacheCleaner;
	}

	/**
	 * @return Tx_Extracache_Domain_Service_CacheCleaner
	 */
	protected function createCacheCleaner() {
		return GeneralUtility::makeInstance('Tx_Extracache_Domain_Service_CacheCleaner');
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
}