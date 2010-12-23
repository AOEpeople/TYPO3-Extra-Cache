<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2010 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * @package extracache_tests
 * @subpackage System_Event
 */
class Tx_Extracache_System_ContentProcessor_Fixtures_DummyContentProcessor implements Tx_Extracache_System_ContentProcessor_Interface {
	/**
	 * function that is called to modify the content
	 *
	 * @param string $content the content to be modified
	 * @return string modified content
	 */
	public function processContent($content) {
		return '[start]'.$content.'[stop]';
	}
	/**
	 * handle exception
	 * @param Exception $e
	 */
	public function handleException(Exception $e) {
		
	}
}