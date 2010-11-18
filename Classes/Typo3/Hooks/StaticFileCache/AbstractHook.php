<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 AOE media GmbH <dev@aoemedia.de>
*  All rights reserved
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

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
		$this->getConfigurationManager()->getArgumentRepository();
	}
	/**
	 * Gets an instance of the configuration manager.
	 *
	 * @return Tx_Extracache_Configuration_ConfigurationManager 
	 */
	protected function getConfigurationManager() {
		if ($this->configurationManager === NULL) {
			$this->configurationManager = t3lib_div::makeInstance('Tx_Extracache_Configuration_ConfigurationManager');
		}
		return $this->configurationManager;
	}
	/**
	 * @return Tx_Extracache_System_Event_Dispatcher
	 */
	protected function getEventDispatcher() {
		if ($this->eventDispatcher === NULL) {
			$this->eventDispatcher = t3lib_div::makeInstance('Tx_Extracache_System_Event_Dispatcher');
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
			$this->extensionManager = t3lib_div::makeInstance('Tx_Extracache_Configuration_ExtensionManager');
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
		return t3lib_div::makeInstance(
			'Tx_Extracache_System_Event_Events_EventOnStaticFileCache',
			$name, $this, $information, $parent, $frontend
		);
	}
	/**
	 * @return Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	protected function getTypo3DbBackend() {
		if($this->typo3DbBackend === NULL) {
			$this->typo3DbBackend = t3lib_div::makeInstance('Tx_Extracache_System_Persistence_Typo3DbBackend');
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