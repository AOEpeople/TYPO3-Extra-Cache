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
 * @package extracache_tests
 * @subpackage System_StaticCache_Fixtures
 */
class ExtendedStaticFileCacheManager extends Tx_Extracache_System_StaticCache_StaticFileCacheManager {
	/**
	 * @return	string
	 */
	public function getCachedFolder() {
		return parent::getCachedFolder();
	}
	/**
	 * @return	string		The cached representation of the current request
	 */
	public function getCachedRepresentation() {
		return parent::getCachedRepresentation();
	}
}