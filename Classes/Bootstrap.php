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
 * @package eft
 */
final class Bootstrap {
	/**
	 * start
	 */
	static public function start() {
		self::initializeClassLoader();
		self::initializeConstants();

		// this configurations must later be copied into the eft-extension
		self::initializeDefaultCleanerStrategies();
		self::initializeDefaultArguments();
		
		self::initializeEventHandling();
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
	 * Initializes the event-handling
	 *
	 * @return void
	 */
	static protected function initializeEventHandling() {
		$dispatcher = t3lib_div::makeInstance('Tx_Extracache_System_Event_Dispatcher');
		$dispatcher->addLazyLoadingHandler('onStaticCacheRequest', 'Tx_Extracache_System_StaticCache_EventHandler', 'handleEventOnStaticCacheRequest');
	}
}