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
class Tx_Extracache_Domain_Repository_ContentProcessorDefinitionRepository implements t3lib_Singleton {
	/**
	 * @var array
	 */
	private $contentProcessorDefinitions = array();

	/**
	 * @param Tx_Extracache_Domain_Model_Argument $argument
	 */
	public function addContentProcessorDefinition(Tx_Extracache_Domain_Model_ContentProcessorDefinition $contentProcessorDefinition) {
		$this->contentProcessorDefinitions[] = $contentProcessorDefinition;
	}
	/**
	 * @return array
	 */
	public function getContentProcessorDefinitions() {
		return $this->contentProcessorDefinitions;
	}
}