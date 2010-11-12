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
class Tx_Extracache_Domain_Repository_CleanerStrategyRepository implements t3lib_Singleton {
	/**
	 * @var array
	 */
	private $cleanerStrategies = array();

	/**
	 * @param Tx_Extracache_Domain_Model_CleanerStrategy $strategy
	 */
	public function addStrategy(Tx_Extracache_Domain_Model_CleanerStrategy $strategy) {
		$this->cleanerStrategies[] = $strategy;
	}
	/**
	 * @param	string $key
	 * @return	Tx_Extracache_Domain_Model_CleanerStrategy
	 * @throws	LogicException
	 */
	public function getStrategy($key) {
		foreach($this->getAllStrategies() as $strategy) {
			if($strategy->getKey() === $key) {
				return $strategy;
			}
		}
		throw new LogicException('strategy '.$key.' is unknown (check with "hasStrategy" before calling "getStrategy")!');
	}
	/**
	 * @return array
	 */
	public function getAllStrategies() {
		return $this->cleanerStrategies;
	}
	/**
	 * @param	string $key
	 * @return	boolean
	 */
	public function hasStrategy($key) {
		$hasStrategy = false;
		foreach($this->getAllStrategies() as $strategy) {
			if($strategy->getKey() === $key) {
				$hasStrategy = true;
				break;
			}
		}
		return $hasStrategy;
	}
}