<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 AOE GmbH <dev@aoe.com>
*  All rights reserved
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Hook class for TYPO3 - execute contentProcessors
 * 
 * Attention: this class-name must begin with 'tx' and NOT with 'Tx'...otherwise this hook will not work!
 * 
 * @package extracache
 * @subpackage Typo3_Hooks
 */
class tx_Extracache_Typo3_Hooks_ExecuteContentProcessor {
	/**
	 * @var Tx_Extracache_Configuration_ExtensionManager
	 */
	private $extensionManager;

	/**
	 * execute contentProcessors
	 *
	 * @param	array		$parameters Parameters delivered by the calling parent object (not used here)
	 * @param	TypoScriptFrontendController	$frontend The calling parent object
	 * @return	void
	 */
	public function executeContentProcessor(array $parameters, TypoScriptFrontendController $frontend) {
		if($this->getExtensionManager()->areContentProcessorsEnabled()) {
			$frontend->content = $this->getContentProcessorChain()->process($frontend->content);
		}
	}

	/**
	 * @return Tx_Extracache_System_ContentProcessor_Chain
	 */
	protected function getContentProcessorChain() {
		return GeneralUtility::makeInstance('Tx_Extracache_System_ContentProcessor_ChainFactory')->getInitialisedChain();
	}
	/**
	 * @return Tx_Extracache_Configuration_ExtensionManager
	 */
	protected function getExtensionManager() {
		if($this->extensionManager === NULL) {
			$this->extensionManager = GeneralUtility::makeInstance('Tx_Extracache_Configuration_ExtensionManager');
		}
		return $this->extensionManager;
	}
}
