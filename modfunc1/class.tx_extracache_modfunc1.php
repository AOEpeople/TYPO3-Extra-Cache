<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2010  <>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use \TYPO3\CMS\Core\Utility\GeneralUtility;

require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Classes' . DIRECTORY_SEPARATOR . 'Controller' . DIRECTORY_SEPARATOR . 'CacheManagementController.php';

/**
 * Module extension (addition to function menu) 'Static Cache Files' for the 'staticfilecache_mananger' extension.
 *
 * @package	extracache
 */
class tx_extracache_modfunc1 extends \TYPO3\CMS\Backend\Module\AbstractFunctionModule {
	/**
	 * @var Tx_Extracache_Controller_CacheManagementController
	 */
	private $cacheManagementController;
	/**
	 * Returns the module menu
	 *
	 * @return	Array with menuitems
	 */
	function modMenu() {
		return Array ("tx_extracache_modfunc1_check" => "" );
	}

	/**
	 * Main method of the module
	 *
	 * @return	HTML
	 */
	function main() {
		// Initializes the module. Done in this function because we may need to re-initialize if data is submitted!
		global $SOBE, $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;

		$cacheDatabaseEntryRepositoryForTableEventlog  = GeneralUtility::makeInstance ( 'Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository' );
		$cacheDatabaseEntryRepositoryForTableEventqueue  = GeneralUtility::makeInstance ( 'Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository' );
		$cacheDatabaseEntryRepositoryForTablePages  = GeneralUtility::makeInstance ( 'Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository' );
		$cacheDatabaseEntryRepositoryForTableStaticCache = GeneralUtility::makeInstance ( 'Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository' );
		$cacheFileRepository = GeneralUtility::makeInstance ( 'Tx_Extracache_Domain_Repository_CacheFileRepository' );
		$extensionManager = GeneralUtility::makeInstance('Tx_Extracache_Configuration_ExtensionManager');
		$view = GeneralUtility::makeInstance ( 'Tx_Extracache_View_View' );
		$GLOBALS['LANG']->includeLLFile('EXT:extracache/Resources/Private/Language/locallang.xml');
		$this->cacheManagementController = GeneralUtility::makeInstance ( 'Tx_Extracache_Controller_CacheManagementController', $cacheDatabaseEntryRepositoryForTableEventlog, $cacheDatabaseEntryRepositoryForTableEventqueue, $cacheDatabaseEntryRepositoryForTablePages, $cacheDatabaseEntryRepositoryForTableStaticCache, $cacheFileRepository, $extensionManager, $view);

		$action = t3lib_div::_GP ( 'action' );
		if(empty($action)){
			$action = 'index';
		}
		$action = $action.'Action';
		$output = call_user_func(array($this->cacheManagementController, $action));
		$output.= '<p class="c-refresh"><a href="'.htmlspecialchars(t3lib_div::getIndpEnv('REQUEST_URI')).'"><img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/refresh_n.gif','width="14" height="14"').' title="'.$LANG->sL('LLL:EXT:lang/locallang_core.php:labels.refresh',1).'" alt="" />'.$LANG->sL('LLL:EXT:lang/locallang_core.php:labels.refresh',1).'</a></p>';
		return $output;
	}
}

if (defined ( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/staticfilecache_mananger/modfunc1/class.tx_extracache_modfunc1.php']) {
	include_once ($TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/staticfilecache_mananger/modfunc1/class.tx_extracache_modfunc1.php']);
}