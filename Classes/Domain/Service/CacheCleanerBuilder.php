<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 AOE media GmbH <dev@aoemedia.de>
*  All rights reserved
*
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

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
		$cacheCleaner = $this->createCacheCleaner();

		$strategies = explode(',', $page['tx_extracache_cleanerstrategies']);
		foreach ($strategies as $strategy) {
			if($this->getCleanerStrategyRepository()->hasStrategy($strategy)) {
				$cacheCleaner->addCleanerInstruction( $this->getCleanerStrategyRepository()->getStrategy($strategy), (integer) $page['uid'] );
			}
		}
		return $cacheCleaner;
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
}