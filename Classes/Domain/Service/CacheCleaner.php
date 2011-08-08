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

// if this class is used in TYPO3_BE-mode, the nc_staticfilecache-class is under certain circumstances unknown, so we must require them
if(class_exists('tx_ncstaticfilecache') === FALSE) {
	require_once t3lib_extMgm::extPath ( 'nc_staticfilecache' ) . 'class.tx_ncstaticfilecache.php';
}

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
			$this->cleanerInstructionRepository = t3lib_div::makeInstance('Tx_Extracache_Domain_Repository_CleanerInstructionRepository');
		}
		return $this->cleanerInstructionRepository;
	}
	/**
	 * @return tx_ncstaticfilecache
	 */
	protected function getStaticFileCache() {
		if($this->staticFileCache === NULL) {
			$this->staticFileCache = t3lib_div::makeInstance('tx_ncstaticfilecache');
		}
		return $this->staticFileCache;
	}
	/**
	 * @return t3lib_TCEmain
	 */
	protected function getTceMain() {
		if($this->tceMain === NULL) {
			$this->tceMain = t3lib_div::makeInstance('t3lib_TCEmain');
			$this->tceMain->start(array(), array());
		}
		return $this->tceMain;
	}
	/**
	 * @return Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	protected function getTypo3DbBackend() {
		if($this->typo3DbBackend === NULL) {
			$this->typo3DbBackend =t3lib_div::makeInstance('Tx_Extracache_System_Persistence_Typo3DbBackend');
		}
		return $this->typo3DbBackend;
	}

	/**
	 * @param	Tx_Extracache_Domain_Model_CleanerStrategy $cleanerStrategy
	 * @param	integer $pageId
	 * @return	Tx_Extracache_Domain_Model_CleanerInstruction
	 */
	private function createCleanerInstruction(Tx_Extracache_Domain_Model_CleanerStrategy $cleanerStrategy, $pageId) {
		return t3lib_div::makeInstance('Tx_Extracache_Domain_Model_CleanerInstruction', $this->getStaticFileCache(), $this->getTceMain(), $this->getTypo3DbBackend(), $cleanerStrategy, $pageId);
	}
}