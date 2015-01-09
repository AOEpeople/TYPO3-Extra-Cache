<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 AOE GmbH <dev@aoe.com>
*  All rights reserved
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Hook for nc_staticfilecache that is called on creating the file with the cached content.
 *
 * @package extracache
 * @subpackage Typo3_Hooks_StaticFileCache
 *
 */
abstract class Tx_Extracache_Typo3_Hooks_StaticFileCache_AbstractHook {
	const FIELD_GroupList = 'tx_extracache_grouplist';

	/**
	 * @var Tx_Extracache_Configuration_ConfigurationManager
	 */
	private $configurationManager;
	/**
	 * @var Tx_Extracache_System_Event_Dispatcher
	 */
	private $eventDispatcher;
	/**
	 * @var Tx_Extracache_Configuration_ExtensionManager
	 */
	private $extensionManager;
	/**
	 * @var Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	private $typo3DbBackend;

	/**
	 * Gets an instance of the argument repository.
	 *
	 * @return Tx_Extracache_Domain_Repository_ArgumentRepository
	 */
	protected function getArgumentRepository() {
		return $this->getConfigurationManager()->getArgumentRepository();
	}
	/**
	 * Gets an instance of the configuration manager.
	 *
	 * @return Tx_Extracache_Configuration_ConfigurationManager 
	 */
	protected function getConfigurationManager() {
		if ($this->configurationManager === NULL) {
			$this->configurationManager = GeneralUtility::makeInstance('Tx_Extracache_Configuration_ConfigurationManager');
		}
		return $this->configurationManager;
	}
	/**
	 * @return Tx_Extracache_System_Event_Dispatcher
	 */
	protected function getEventDispatcher() {
		if ($this->eventDispatcher === NULL) {
			$this->eventDispatcher = GeneralUtility::makeInstance('Tx_Extracache_System_Event_Dispatcher');
		}
		return $this->eventDispatcher;
	}
	/**
	 * Gets an instance of the extension configuration manager.
	 *
	 * @return Tx_Extracache_Configuration_ExtensionManager
	 */
	protected function getExtensionManager() {
		if ($this->extensionManager === NULL) {
			$this->extensionManager = GeneralUtility::makeInstance('Tx_Extracache_Configuration_ExtensionManager');
		}
		return $this->extensionManager;
	}
	/**
	 * Gets a new event.
	 *
	 * @param string $name
	 * @param array $information
	 * @param tx_ncstaticfilecache $parent
	 * @param tslib_fe $frontend (optional)
	 * @return Tx_Extracache_System_Event_Events_Event
	 */
	protected function getNewEvent($name, array $information, tx_ncstaticfilecache $parent, tslib_fe $frontend = NULL) {
		return GeneralUtility::makeInstance(
			'Tx_Extracache_System_Event_Events_EventOnStaticFileCache',
			$name, $this, $information, $parent, $frontend
		);
	}
	/**
	 * @return Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	protected function getTypo3DbBackend() {
		if($this->typo3DbBackend === NULL) {
			$this->typo3DbBackend = GeneralUtility::makeInstance('Tx_Extracache_System_Persistence_Typo3DbBackend');
		}
		return $this->typo3DbBackend;
	}

	/**
	 * Determines whether an extension is loaded.
	 *
	 * @param  string $extensionKey
	 * @return boolean
	 */
	protected function isExtensionLoaded($extensionKey) {
		return t3lib_extMgm::isLoaded($extensionKey);
	}
}