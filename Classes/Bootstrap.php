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
 * The bootstrap of extracache extension, initialising basic configurations
 * @package extracache
 */
final class Bootstrap {
	const ExtensionKey = 'extracache';

	/**
	 * start
	 */
	static public function start() {
		self::initializeClassLoader();
		self::initializeConstants();

/*
		self::initializeEventHandling();
		self::initializeHooks();
		self::initializeXClasses();
*/
		self::initializeSchedulerTasks();

		// this configurations must later be copied into the eft-extension
		self::initializeDefaultArguments();
		self::initializeDefaultCleanerStrategies();
		self::initializeDefaultCacheCleanerEvents();
	}
	
	/**
	 * Initializes the autoload mechanism of Extbase. This is supplement to the core autoloader.
	 *
	 * @return void
	 */
	static protected function initializeClassLoader() {
		if (!class_exists('Tx_Extbase_Utility_ClassLoader')) {
			require(t3lib_extmgm::extPath('extbase') . 'Classes/Utility/ClassLoader.php');
		}
		$classLoader = new Tx_Extbase_Utility_ClassLoader();
		spl_autoload_register(array($classLoader, 'loadClass'));
	}
	/**
	 * load some classes so that we can use constants of that classes anywhere
	 */
	static protected function initializeConstants() {
		$classLoader = new Tx_Extbase_Utility_ClassLoader();
		$classLoader->loadClass( 'Tx_Extracache_Domain_Model_Argument' );
		$classLoader->loadClass( 'Tx_Extracache_Domain_Model_CleanerStrategy' );
	}
	/**
	 * Initializes hooks.
	 *
	 * @return void
	 */
	static protected function initializeHooks() {
		// Register hooks for nc_staticfilecache-extension
		$staticFileCacheHooks =& $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['nc_staticfilecache/class.tx_ncstaticfilecache.php'];
		$hookDirectory = 'EXT:' . self::ExtensionKey . '/Classes/Typo3/Hooks/StaticFileCache/';
		$staticFileCacheHooks['createFile_initializeVariables'][self::ExtensionKey] = $hookDirectory . 'CreateFileHook.php:CreateFileHook->initialize';
		$staticFileCacheHooks['createFile_processContent'][self::ExtensionKey] = $hookDirectory . 'CreateFileHook.php:CreateFileHook->process';
		$staticFileCacheHooks['processDirtyPages'][self::ExtensionKey] = $hookDirectory . 'CreateFileHook.php:DirtyPagesHook->process';

		// Register Hook that determine, block and re-queue modifications concerning file references (This is required in combination with statically cached files):
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = PATH_tx_extracache . 'Typo3/Hooks/FileReferenceModification.php:&Tx_Extracache_Typo3_Hooks_FileReferenceModification';

		// Register pre-rendering cache to deliver statically published content:
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/index_ts.php']['preprocessRequest'][] = 'EXT:'.self::ExtensionKey.'/Classes/System/StaticCache/Dispatcher.php:&Tx_Extracache_System_StaticCache_Dispatcher->dispatch';

		// register hook to disable caching for faulty pages (e.g. if templaVoila could not render page):
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'][] = 'EXT:'.self::ExtensionKey.'/Typo3/Hooks/AvoidFaultyPagesHook.php:&Tx_Extracache_Typo3_Hooks_AvoidFaultyPagesHook->disableCachingOnFaultyPages';

		// Sends HTTP headers for debuging caching situations (if developmentContext is set)
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][self::ExtensionKey] = 'EXT:'.self::ExtensionKey.'/Typo3/Hooks/SendCacheDebugHeader.php:&Tx_Extracache_Typo3_Hooks_SendCacheDebugHeader->sendCacheDebugHeader';

		// Register hook that ignores an existing TYPO3 cache (used to force regeneration):
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['headerNoCache'][self::ExtensionKey] = 'EXT:'.self::ExtensionKey.'/Typo3/Hooks/IgnoreTypo3Cache.php:Tx_Extracache_Typo3_Hooks_IgnoreTypo3Cache->ignoreExistingCache';

		// Register hook to write gr_list to cache_pages:
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['insertPageIncache'][] = 'EXT:'.self::ExtensionKey.'/Typo3/Hooks/InsertPageIncache.php:&Tx_Extracache_Typo3_Hooks_InsertPageIncache';
	}
	/**
	 * Initializes scheduler-tasks.
	 *
	 * @return void
	 */
	static protected function initializeSchedulerTasks() {
		if (TYPO3_MODE == 'BE') {
			// register scheduler-task to clean-up removed files:
			require_once PATH_tx_extracache . 'Classes/Typo3/SchedulerTaskCleanUpRemovedFiles.php';
			$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Tx_Extracache_Typo3_SchedulerTaskCleanUpRemovedFiles'] = array (
				'extension'        => self::ExtensionKey,
				'title'            => 'LLL:EXT:' . self::ExtensionKey . '/Resources/Private/Language/locallang_db.xml:scheduler_task_cleanUpRemovedFiles.name',
				'description'      => 'LLL:EXT:' . self::ExtensionKey . '/Resources/Private/Language/locallang_db.xml:scheduler_task_cleanUpRemovedFiles.description',
			);
		}
	}
	/**
	 * Initializes XCLASSES
	 */
	static protected function initializeXClasses() {
		// Define XCLASS for nc_staticfilecache info module:
		$GLOBALS['TYPO3_CONF_VARS']['BE']['XCLASS']['ext/nc_staticfilecache/infomodule/class.tx_ncstaticfilecache_infomodule.php'] = PATH_tx_extracache . 'Classes/Controller/ExtendedStaticFileCacheInfoModule.php';
	}

	/**
	 * initialize some default cleanerStrategies
	 */
	static protected function initializeDefaultArguments() {
		/** @var $configurationManager Tx_Extracache_Configuration_ConfigurationManager */
		$configurationManager = t3lib_div::makeInstance('Tx_Extracache_Configuration_ConfigurationManager');
		$configurationManager->addArgument(Tx_Extracache_Domain_Model_Argument::TYPE_whitelist, 'eft', true);
		$configurationManager->addArgument(Tx_Extracache_Domain_Model_Argument::TYPE_whitelist, 'eftdslconfig', true);
		$configurationManager->addArgument(Tx_Extracache_Domain_Model_Argument::TYPE_whitelist, 'eftprepaidconfig', true);
		$configurationManager->addArgument(Tx_Extracache_Domain_Model_Argument::TYPE_whitelist, 'tx_t3blog_pi1', true);
		$configurationManager->addArgument(Tx_Extracache_Domain_Model_Argument::TYPE_whitelist, 'tx_aoesurvey_pi1', true);

		$value = array('action' => array('getAdressAjax', 'getPaymentDebitDataAjax'));
		$configurationManager->addArgument(Tx_Extracache_Domain_Model_Argument::TYPE_unprocessible, '*', $value);
		$value = array('action' => array('getCompanionProductsAjax', 'showMobilePhoneProductListAjax'));
		$configurationManager->addArgument(Tx_Extracache_Domain_Model_Argument::TYPE_unprocessible, 'eftproductchange', $value);
		$value = array('action' => array('getCompanionProductsAjax','showMobilePhoneProductListAjax','updateCompanionProductOptionsAjax'));
		$configurationManager->addArgument(Tx_Extracache_Domain_Model_Argument::TYPE_unprocessible, 'eftPostpaidMobilePhone', $value);
		$configurationManager->addArgument(Tx_Extracache_Domain_Model_Argument::TYPE_unprocessible, 'eID', true);
		$value = array('action' => array('index', 'upload', 'publish', 'download', 'new', 'create'));
		$configurationManager->addArgument(Tx_Extracache_Domain_Model_Argument::TYPE_unprocessible, 'tx_congstarizer_pi1', $value);
		$value = array('action' => array('create', 'update', 'delete'));
		$configurationManager->addArgument(Tx_Extracache_Domain_Model_Argument::TYPE_unprocessible, 'tx_congstarizer_pi2', $value);
		
		$configurationManager->addArgument(Tx_Extracache_Domain_Model_Argument::TYPE_ignoreOnCreatingCache, 'eftbasket', true);
		$configurationManager->addArgument(Tx_Extracache_Domain_Model_Argument::TYPE_ignoreOnCreatingCache, 'cHash', true);
		$configurationManager->addArgument(Tx_Extracache_Domain_Model_Argument::TYPE_ignoreOnCreatingCache, 'ftu', true);

		require_once(PATH_tx_eft.'domain/service/campaign/class.tx_eft_domain_service_campaign_affiliateEntryDetection.php');
		$configurationManager->addArgument(Tx_Extracache_Domain_Model_Argument::TYPE_ignoreOnCreatingCache, tx_eft_domain_service_campaign_affiliateEntryDetection::AFFILINET_PARAMETER, true);
		$configurationManager->addArgument(Tx_Extracache_Domain_Model_Argument::TYPE_ignoreOnCreatingCache, tx_eft_domain_service_campaign_affiliateEntryDetection::FRIEND_PARAMETER, true);
		$configurationManager->addArgument(Tx_Extracache_Domain_Model_Argument::TYPE_ignoreOnCreatingCache, tx_eft_domain_service_campaign_affiliateEntryDetection::FRIEND_PARAMETER_STARSELLER, true);
		$configurationManager->addArgument(Tx_Extracache_Domain_Model_Argument::TYPE_ignoreOnCreatingCache, tx_eft_domain_service_campaign_affiliateEntryDetection::GLOBAL_GROUP_PARAMETER, true);
		$configurationManager->addArgument(Tx_Extracache_Domain_Model_Argument::TYPE_ignoreOnCreatingCache, tx_eft_domain_service_campaign_affiliateEntryDetection::GOOGLE_PARAMETER, true);
		$configurationManager->addArgument(Tx_Extracache_Domain_Model_Argument::TYPE_ignoreOnCreatingCache, tx_eft_domain_service_campaign_affiliateEntryDetection::MISSIONONE_PARAMETER, true);
		$configurationManager->addArgument(Tx_Extracache_Domain_Model_Argument::TYPE_ignoreOnCreatingCache, tx_eft_domain_service_campaign_affiliateEntryDetection::TKWORLD_PARAMETER, true);
		$configurationManager->addArgument(Tx_Extracache_Domain_Model_Argument::TYPE_ignoreOnCreatingCache, tx_eft_domain_service_campaign_affiliateEntryDetection::YAHOO_PARAMETER, true);
		$configurationManager->addArgument(Tx_Extracache_Domain_Model_Argument::TYPE_ignoreOnCreatingCache, tx_eft_domain_service_campaign_affiliateEntryDetection::ZANOX_PARAMETER, true);
		$configurationManager->addArgument(Tx_Extracache_Domain_Model_Argument::TYPE_ignoreOnCreatingCache, tx_eft_domain_service_campaign_affiliateEntryDetection::ZED_PARAMETER, true);
	}
	/**
	 * initialize some default cleanerStrategies
	 */
	static protected function initializeDefaultCleanerStrategies() {
		/** @var $configurationManager Tx_Extracache_Configuration_ConfigurationManager */
		$configurationManager = t3lib_div::makeInstance('Tx_Extracache_Configuration_ConfigurationManager');

		$actions = Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_TYPO3Clear + Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_StaticUpdate;
		$childrenMode = Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenNoAction;
		$elementsMode = Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsNoAction;
		$key = '1';
		$name = 'Update: Seite (ohne varianten)';
		$configurationManager->addCleanerStrategy($actions, $childrenMode, $elementsMode, $key, $name);
		
		$actions = Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_TYPO3Clear + Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_StaticClear;
		$childrenMode = Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenNoAction;
		$elementsMode = Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsOnly;
		$key = '2';
		$name = 'Clear: nur Seitenvarianten (Elemente)';
		$configurationManager->addCleanerStrategy($actions, $childrenMode, $elementsMode, $key, $name);

		$actions = Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_TYPO3Clear + Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_StaticClear;
		$childrenMode = Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenWithParent;
		$elementsMode = Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsWithParent;
		$key = '3';
		$name = 'Clear: Seite und Unterseiten (alles)';
		$configurationManager->addCleanerStrategy($actions, $childrenMode, $elementsMode, $key, $name);

		$actions = Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_TYPO3Clear + Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_StaticUpdate;
		$childrenMode = Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenNoAction;
		$elementsMode = Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsWithParent;
		$key = '4';
		$name = 'Update: Seite und Varianten der Seite';
		$configurationManager->addCleanerStrategy($actions, $childrenMode, $elementsMode, $key, $name);
	}
	/**
	 * initialize some default cacheCleanerEvents
	 */
	static protected function initializeDefaultCacheCleanerEvents() {
		/** @var $configurationManager Tx_Extracache_Configuration_ConfigurationManager */
		$configurationManager = t3lib_div::makeInstance('Tx_Extracache_Configuration_ConfigurationManager');
		$configurationManager->addEvent('onUpdateProductCatalogueForClientId1', 'Produktkatalog-Update [client: congstar]');
		$configurationManager->addEvent('onUpdateProductCatalogueForClientId2', 'Produktkatalog-Update [client: ebay]');
		$configurationManager->addEvent('onUpdateProductCatalogueForClientId3', 'Produktkatalog-Update [client: RTL]');
		$configurationManager->addEvent('onAddCongstarizerPicture', 'congstarizer-Bild in Gallerie einfügen');		
	}
	/**
	 * initialize event-handler
	 */
	static protected function initializeEventHandling() {
		$dispatcher = t3lib_div::makeInstance('Tx_Extracache_System_Event_Dispatcher');
		self::addEventHandlerForLogging ( $dispatcher );
		self::addEventHandlerForStaticCache ( $dispatcher );
	}
	/**
	 * @param Tx_Extracache_System_Event_Dispatcher $dispatcher
	 */
	static protected function addEventHandlerForLogging(Tx_Extracache_System_Event_Dispatcher $dispatcher) {
		$dispatcher->addLazyLoadingHandler('onCleanUpRemovedFilesError', 'Tx_Extracache_System_LoggingEventHandler', 'logWarning');
		$dispatcher->addLazyLoadingHandler('onProcessCacheEventInfo', 'Tx_Extracache_System_LoggingEventHandler', 'logInfo');
		$dispatcher->addLazyLoadingHandler('onProcessCacheEventError', 'Tx_Extracache_System_LoggingEventHandler', 'logWarning');
		$dispatcher->addLazyLoadingHandler('onStaticCacheInfo', 'Tx_Extracache_System_LoggingEventHandler', 'logInfo');
		$dispatcher->addLazyLoadingHandler('onStaticCacheLoaded', 'Tx_Extracache_System_LoggingEventHandler', 'logNotice');
		$dispatcher->addLazyLoadingHandler('onStaticCacheWarning', 'Tx_Extracache_System_LoggingEventHandler', 'logWarning');
		$dispatcher->addLazyLoadingHandler('onStaticCacheFatalError', 'Tx_Extracache_System_LoggingEventHandler', 'logFatalError');
	}
	/**
	 * @param Tx_Extracache_System_Event_Dispatcher $dispatcher
	 */
	static protected function addEventHandlerForStaticCache(Tx_Extracache_System_Event_Dispatcher $dispatcher) {
		$dispatcher->addLazyLoadingHandler('onProcessCacheEvent', 'Tx_Extracache_Domain_Service_CacheEventHandler', 'handleEventOnProcessCacheEvent');
		$dispatcher->addLazyLoadingHandler('onStaticCacheRequest', 'Tx_Extracache_System_StaticCache_EventHandler', 'handleEventOnStaticCacheRequest');
	}
}