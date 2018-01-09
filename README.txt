Overview of system related internals used or defined by the extension 'extracache'
------------------------------------------------------------------------------------------------------------------------

1) What does this extension provides?
	* Cache TYPO3-pages with nc_staticfilecache and modify statically cached content before sending it to the client
	* Cache TYPO3-pages with nc_staticfilecache for different FE-user-groups (if option 'supportFeUsergroups' is enabled)
	* Cache TYPO3-pages with nc_staticfilecache even if the URL contain GET-params (this extension can cache and/or ignore GET-params)
	* Delete or update statically cached content if a certain event occur
	* Support of contentProcessors to modify content, before the content will be send to the client
	* Support of events
	* BE-modul for admins to show infos and delete statically cached content
	* Scheduler-Task to clean-up removed files (files will not be deleted immediately (if editor delete them inside the TYPO3-BE), because
	  statically cached content maybe have references to that files). Execute this scheduler-task not till then all statically cached content
	  have been deleted or updated, so they have no more references to the deleted files!
	* Scheduler-Task to process cache-events. If you define cache-events and they should not processed instantly, than you must use this
	  scheduler-task to process them later.


2) How does this extension works?
	2.1) Save content to staticCache
		1. TYPO3-Frontend must be called (normaly) via index.php
		2. At the End of the page-generation-process, nc_staticfilecache will create (if possible) a staticCache- and database-entry.
		   nc_staticfilecache provides some hooks to modify the content, which should be cached. This extension uses that hooks, to
		   modify the content, so that we can e.g. support different FE-usergroups.

	2.2) LOAD statically cached content
		1. TYPO3-Frontend must be called (normaly) via index.php
		2. 'preprocessRequest'-Hook in tslib/index_ts.php will be used, to check, if the request can be responsed via statically cached content
			(the statically cached content must be written before via nc_staticfilecache)
		3. If statically cached content is available, modify the cached content (if required) and send it to the client


3) Which interfaces provides this extension?
	* You can define several arguments (take a look at class Tx_Extracache_Domain_Model_Argument for further information):
		\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_Extracache_Configuration_ConfigurationManager')->addArgument( [type], [name], [value] );

	* You can define several cache-cleanerStrategies (take a look at class Tx_Extracache_Domain_Model_CleanerStrategy for further information):
		\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_Extracache_Configuration_ConfigurationManager')->addCleanerStrategy( [actions], [childrenMode], [elementsMode], [key], [name] );

	* You can define several cache-events (take a look at class Tx_Extracache_Domain_Model_Event for further information):
		\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_Extracache_Configuration_ConfigurationManager')->addEvent( [key], [name], [interval] );

	* You can use several events to modfiy/add logic (the most important events are defined in Classes/System/Event/Events/) if you add your own eventHandler to Tx_Extracache_System_Event_Dispatcher: 
		\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_Extracache_System_Event_Dispatcher')->addHandler( [eventName], [handlerObject], [handlerObjectMethod] );
		\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_Extracache_System_Event_Dispatcher')->addLazyLoadingHandler( [eventName], [handlerObjectName], [handlerObjectMethod] );

	* You can trigger the event 'onFaultyPages' to define, that the called page should not be cached statically (because e.g. an error/exception occured, so the generated page maybe contains a warning, which you don't want to cache statically):
		\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_Extracache_System_Event_Dispatcher')->triggerEvent( 'onFaultyPages' );
		OR
		$event = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_Extracache_System_Event_Events_EventOnFaultyPages');
		\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_Extracache_System_Event_Dispatcher')->triggerEvent( $event );

	* You can trigger cache-events:
		$event = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_Extracache_System_Event_Events_EventOnProcessCacheEvent', [cacheEvent]);
		\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_Extracache_System_Event_Dispatcher')->triggerEvent( $event );

	* You can define contentProcessors (which modify the content before the content will be send to the client):
		1. You must enable contentProcessors in the Extension-Manager of this extension
		2. You must implement one or more contentProcessors in your own extension (your contentProcessor must implement the interface Tx_Extracache_System_ContentProcessor_Interface)
		3. You must add your contentProcessor to this extension:
			\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_Extracache_Configuration_ConfigurationManager')->addContentProcessorDefinition([classNameOfYourContentProcessor], [pathToYourContentProcessorIncludeThePhpFile])


4) How can i define this:
	4.1) Delete statically cached content of all subpages and all variants of page X and update statically cached content of page X if event 'onUpdateProductCatalogue' occur?
			1. Install extension 'nc_staticfilecache'

			2. activate option 'markDirtyInsteadOfDeletion' of nc_staticfilecache-Extension inside the extension-manager (this is important, so we can delete the TYPO3-cache, but the statically cached content is still there)

			3. define cache-cleanerStrategies:
			$configurationManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_Extracache_Configuration_ConfigurationManager');
			$configurationManager->addCleanerStrategy(Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_TYPO3Clear, Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenWithParent, Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsNoAction, 'clear_frontendcache_all', 'Clear: Frontend-Cache (page with subpages)');
			$configurationManager->addCleanerStrategy(Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_StaticClear, Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenOnly, Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsWithParent, 'clear_subpages_with_elements', 'Clear: subpages (with variants)');
			$configurationManager->addCleanerStrategy(Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_StaticClear, Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenNoAction, Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsOnly, 'clear_page_only_elements', 'Clear: page (only variants)');
			$configurationManager->addCleanerStrategy(Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_StaticUpdate, Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenNoAction, Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsNoAction, 'update_page_without_elements', 'Update: page (without variants)');

			4. define cache-event:
			$configurationManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_Extracache_Configuration_ConfigurationManager');
			$configurationManager->addEvent( 'onUpdateProductCatalogue', 'productCatalogue was updated' );

			5. Configure page X in TYPO3-BE (add created cache-cleanerStrategies (in correct order, as you defined them) and cache-event to the page-properties of page X)

			6. Process event 'onUpdatedProductCatalogue':
			$event = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_Extracache_System_Event_Events_EventOnProcessCacheEvent', 'onUpdateProductCatalogue');
			\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_Extracache_System_Event_Dispatcher')->triggerEvent( $event );


5) Error codes as delivered to Tx_Extbase_Validation_Validator_AbstractValidator:addError()
	* Tx_Extracache_Validation_Validator_Argument
		+ 1289897741: checkName() -> 'name is not valid'
		+ 1289897742: checkType() -> 'type is not supported'
		+ 1289897743: checkValue() -> 'value is not valid (must be one of {TRUE, is_array, is_string})'
		+ 1289897744: checkValue() -> 'value is an empty array (must have items if is_array)'

	* Tx_Extracache_Validation_Validator_CleanerStrategy
		+ 1289897851: isValid() -> 'cleanerStrategy with key does not exist'
		* 1289897852: actionsAreValid() -> 'actions do not contain any valid action'
		* 1289897853: childrenModeIsValid() -> 'childrenMode is not supported'
		* 1289897854: elementModeIsValid() -> 'elementMode is not supported'

	* Tx_Extracache_Validation_Validator_Event
		+ 1289898441: isValid() -> 'event with key does already exist'
		+ 1291388576: isValid() -> 'interval is not a positive integer-value'
		
		
		
This extension is no longer actively maintained.
