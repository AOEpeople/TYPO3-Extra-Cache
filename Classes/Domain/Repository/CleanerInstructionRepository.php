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
class Tx_Extracache_Domain_Repository_CleanerInstructionRepository {
	/**
	 * @var array
	 */
	private $cleanerInstructions = array();

	/**
	 * @param Tx_Extracache_Domain_Model_CleanerInstruction $cleanerInstruction
	 */
	public function addCleanerInstruction(Tx_Extracache_Domain_Model_CleanerInstruction $cleanerInstruction) {
		$this->cleanerInstructions[] = $cleanerInstruction;
	}
	/**
	 * @return array
	 */
	public function getCleanerInstructions() {
		return $this->cleanerInstructions;
	}
}