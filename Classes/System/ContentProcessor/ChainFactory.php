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
 * @subpackage System_ContentProcessor
 */
class Tx_Extracache_System_ContentProcessor_ChainFactory {
	/**
	 * builds the chain
	 *
	 * @return Tx_Extracache_System_ContentProcessor_Chain
	 */
	public function getInitialisedChain() {
		$chain = $this->createChain();
		foreach($this->getContentProcessorDefinitions() as $contentProcessorDefinition) {
			if($contentProcessorDefinition->getPath() !== NULL) {
				require_once( $contentProcessorDefinition->getPath() );
			}
			$chain->addContentProcessor( $this->createContentProcessor( $contentProcessorDefinition->getClassName() ) );
		}
		return $chain;
	}

	/**
	 * @return Tx_Extracache_System_ContentProcessor_Chain
	 */
	protected function createChain() {
		return t3lib_div::makeInstance('Tx_Extracache_System_ContentProcessor_Chain');
	}
	/**
	 * @return Tx_Extracache_System_ContentProcessor_Interface
	 */
	protected function createContentProcessor($contentProcessorClassName) {
		return t3lib_div::makeInstance($contentProcessorClassName);
	}
	/**
	 * @return array
	 */
	protected function getContentProcessorDefinitions() {
		return t3lib_div::makeInstance('Tx_Extracache_Domain_Repository_ContentProcessorDefinitionRepository')->getContentProcessorDefinitions();
	}
}