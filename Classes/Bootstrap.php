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
		self::initializeHooks();
		self::initializeEventHandling();
		self::initializeXClasses();
*/

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

		// Register pre-rendering cache to deliver statically published content:
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/index_ts.php']['preprocessRequest'][] = 'EXT:'.self::ExtensionKey.'/Classes/System/StaticCache/Dispatcher.php:&Tx_Extracache_System_StaticCache_Dispatcher->dispatch';

		// register hook to disable caching for faulty pages (e.g. if templaVoila could not render page):
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'][] = 'EXT:'.self::ExtensionKey.'/Typo3/Hooks/AvoidFaultyPagesHook.php:&Tx_Extracache_Typo3_Hooks_AvoidFaultyPagesHook->disableCachingOnFaultyPages';

		// Register hook that ignores an existing TYPO3 cache (used to force regeneration):
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['headerNoCache']['extracache'] = 'EXT:'.self::ExtensionKey.'/Typo3/Hooks/IgnoreTypo3Cache.php:Tx_Extracache_Typo3_Hooks_IgnoreTypo3Cache->ignoreExistingCache';
	}
	/**
	 * Initializes XCLASSES
	 */
	static protected function initializeXClasses() {
		// Define XCLASS for nc_staticfilecache info module:
		$GLOBALS['TYPO3_CONF_VARS']['BE']['XCLASS']['ext/nc_staticfilecache/infomodule/class.tx_ncstaticfilecache_infomodule.php'] = t3lib_extMgm::extPath('extracache') . 'Classes/Controller/ExtendedStaticFileCacheInfoModule.php';
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
		$dispatcher->addLazyLoadingHandler('onStaticCacheInfo', 'Tx_Extracache_System_LoggingEventHandler', 'logInfo');
		$dispatcher->addLazyLoadingHandler('onStaticCacheLoaded', 'Tx_Extracache_System_LoggingEventHandler', 'logNotice');
		$dispatcher->addLazyLoadingHandler('onStaticCacheWarning', 'Tx_Extracache_System_LoggingEventHandler', 'logWarning');
		$dispatcher->addLazyLoadingHandler('onStaticCacheFatalError', 'Tx_Extracache_System_LoggingEventHandler', 'logFatalError');
	}
	/**
	 * @param Tx_Extracache_System_Event_Dispatcher $dispatcher
	 */
	static protected function addEventHandlerForStaticCache(Tx_Extracache_System_Event_Dispatcher $dispatcher) {
		$dispatcher->addLazyLoadingHandler('onStaticCacheRequest', 'Tx_Extracache_System_StaticCache_EventHandler', 'handleEventOnStaticCacheRequest');
	}
}