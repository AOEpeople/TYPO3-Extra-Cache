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

use TYPO3\CMS\Core\Utility\GeneralUtility;

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
		return GeneralUtility::makeInstance('Tx_Extracache_System_ContentProcessor_Chain');
	}
	/**
	 * @param	$className
	 * @return	Tx_Extracache_System_ContentProcessor_Interface
	 */
	protected function createContentProcessor($className) {
		return GeneralUtility::makeInstance($className);
	}
	/**
	 * @return array
	 */
	protected function getContentProcessorDefinitions() {
		return GeneralUtility::makeInstance('Tx_Extracache_Domain_Repository_ContentProcessorDefinitionRepository')->getContentProcessorDefinitions();
	}
}