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
class Tx_Extracache_System_ContentProcessor_Chain {
	/**
	 * chain with the registered contentProcessors
	 * @var	array
	 */
	private $chain = array();

	/**
	 * @param Tx_Extracache_System_ContentProcessor_ContentProcessorInterface $processor
	 */
	public function addContentProcessor(Tx_Extracache_System_ContentProcessor_Interface $processor) {
		$this->chain[]=$processor;
	}
	/**
	 * main function for calling the registered contentPocessors
	 * @param	string $content
	 * @return	string
	 */
	public function process($content) {
		if($this->crawlerIsRunning() === FALSE) {
			foreach ($this->chain as $processor) {
				try {
					$content = $processor->processContent( $content );
				} catch(Exception $e) {
					$processor->handleException( $e );
				}
			}
		}
		return $content;
	}

	/**
	 * check if crawler is running to update cache or staticpub
	 * @return boolean
	 */
	protected function crawlerIsRunning() {
		$crawlerIsRunning = FALSE;
		if ($GLOBALS['TSFE']->applicationData['tx_crawler']['parameters']['procInstructions']) {
			$crawlerIsRunning = TRUE;
		}
		return $crawlerIsRunning;
	}
}