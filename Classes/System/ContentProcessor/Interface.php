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
interface Tx_Extracache_System_ContentProcessor_Interface {
	/**
	 * function that is called to modify the content
	 *
	 * @param	string $content the content to be modified
	 * @return	string modified content
	 */
	public function processContent($content);
	/**
	 * handle exception
	 * @param Exception $e
	 */
	public function handleException(Exception $e);
}