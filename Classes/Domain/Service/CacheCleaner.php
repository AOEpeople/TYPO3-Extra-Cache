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
class Tx_Extracache_Domain_Service_CacheCleaner {
	/**
	 * @var Tx_Extracache_Domain_Repository_CleanerInstructionRepository
	 */
	private $cleanerInstructionRepository;
	/**
	 * @var tx_ncstaticfilecache
	 */
	private $staticFileCache;
	/**
	 * @var t3lib_TCEmain
	 */
	private $tceMain;
	/**
	 * @var Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	private $typo3DbBackend;

	/**
	 * @param	Tx_Extracache_Domain_Model_CleanerStrategy $cleanerStrategy
	 * @param	integer $pageId
	 */
	public function addCleanerInstruction(Tx_Extracache_Domain_Model_CleanerStrategy $cleanerStrategy, $pageId) {
		$cleanerInstruction = $this->createCleanerInstruction( $cleanerStrategy, $pageId );
		$this->getCleanerInstructionRepository()->addCleanerInstruction($cleanerInstruction);
	}
	/**
	 * process
	 */
	public function process() {
		foreach($this->getCleanerInstructionRepository()->getCleanerInstructions() as $cleanerInstruction) {
			$cleanerInstruction->process();
		}
	}

	/**
	 * @return Tx_Extracache_Domain_Repository_CleanerInstructionRepository
	 */
	protected function getCleanerInstructionRepository() {
		if($this->cleanerInstructionRepository === NULL) {
			$this->cleanerInstructionRepository = GeneralUtility::makeInstance('Tx_Extracache_Domain_Repository_CleanerInstructionRepository');
		}
		return $this->cleanerInstructionRepository;
	}
	/**
	 * @return tx_ncstaticfilecache
	 */
	protected function getStaticFileCache() {
		if($this->staticFileCache === NULL) {
			$this->staticFileCache = GeneralUtility::makeInstance('tx_ncstaticfilecache');
		}
		return $this->staticFileCache;
	}
	/**
	 * @return t3lib_TCEmain
	 */
	protected function getTceMain() {
		if($this->tceMain === NULL) {
			$this->tceMain = GeneralUtility::makeInstance('t3lib_TCEmain');
			$this->tceMain->start(array(), array());
		}
		return $this->tceMain;
	}
	/**
	 * @return Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	protected function getTypo3DbBackend() {
		if($this->typo3DbBackend === NULL) {
			$this->typo3DbBackend =GeneralUtility::makeInstance('Tx_Extracache_System_Persistence_Typo3DbBackend');
		}
		return $this->typo3DbBackend;
	}

	/**
	 * @param	Tx_Extracache_Domain_Model_CleanerStrategy $cleanerStrategy
	 * @param	integer $pageId
	 * @return	Tx_Extracache_Domain_Model_CleanerInstruction
	 */
	private function createCleanerInstruction(Tx_Extracache_Domain_Model_CleanerStrategy $cleanerStrategy, $pageId) {
		return GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_CleanerInstruction', $this->getStaticFileCache(), $this->getTceMain(), $this->getTypo3DbBackend(), $cleanerStrategy, $pageId);
	}
}