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
 * defines an staticCache-response
 *
 * @package extracache
 * @subpackage System_StaticCache
 */
class Tx_Extracache_System_StaticCache_Response implements t3lib_Singleton {
	/**
	 * @var string
	 */
	protected $content;

	/**
	 * @return string
	 */
	public function getContent() {
		return $this->content;
	}
	/**
	 * @param	string $content
	 * @return	Tx_Extracache_System_StaticCache_Response
	 */
	public function setContent($content) {
		$this->content = $content;
		return $this;
	}
}