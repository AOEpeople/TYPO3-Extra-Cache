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
class ExtendedAbstractManager extends Tx_Extracache_System_StaticCache_AbstractManager {
	/**
	 * @return	string
	 */
	protected function getCachedFolder() {
		return '';
	}
	/**
	 * Gets the cached representation of the current request.
	 *
	 * @return	string		The cached representation of the current request
	 */
	protected function getCachedRepresentation() {
		return '';
	}
}