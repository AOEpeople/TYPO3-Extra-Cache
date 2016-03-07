<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2010 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

#require_once dirname ( __FILE__ ) . '/../../AbstractTestcase.php';

/**
 * Test case for tx_Extracache_Typo3_Hooks_StaticFileCache_CreateFileHook
 *
 * @package extracache_tests
 * @subpackage Typo3_Hooks_StaticFileCache
 */
class Tx_Extracache_Typo3_Hooks_StaticFileCache_CreateFileHookTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var Tx_Extracache_Domain_Repository_ArgumentRepository
	 */
	protected $argumentRepository;
	/**
	 * @var tx_Extracache_Typo3_Hooks_StaticFileCache_CreateFileHook
	 */
	protected $createFileHook;
	/**
	 * @var tx_ncstaticfilecache
	 */
	protected $staticFileCache;
	/**
	 * @var Tx_Extracache_Xclass_FrontendUserAuthentication
	 */
	protected $frontendUser;
	/**
	 * @var tslib_fe
	 */
	protected $frontend;
	/**
	 * @var Tx_Extracache_System_Event_Dispatcher
	 */
	protected $eventDispatcher;
	/**
	 * @var Tx_Extracache_Configuration_ExtensionManager
	 */
	protected $extensionManager;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();

		$this->argumentRepository = new Tx_Extracache_Domain_Repository_ArgumentRepository();
		$this->argumentRepository->addArgument(new Tx_Extracache_Domain_Model_Argument('clean', Tx_Extracache_Domain_Model_Argument::TYPE_ignoreOnCreatingCache, TRUE));
		$this->argumentRepository->addArgument(new Tx_Extracache_Domain_Model_Argument('whitelist', Tx_Extracache_Domain_Model_Argument::TYPE_whitelist, TRUE));

		$this->staticFileCache = $this->getMock('tx_ncstaticfilecache');

		$this->frontendUser = $this->getMock('Tx_Extracache_Xclass_FrontendUserAuthentication', array());
		$this->frontend = $this->getMock ('tslib_fe', array(), array(), '', FALSE);
		$this->frontend->fe_user = $this->frontendUser;

		$this->eventDispatcher = $this->getMock('Tx_Extracache_System_Event_Dispatcher', array('triggerEvent'));
		$this->eventDispatcher->expects($this->any())->method('triggerEvent')->will($this->returnCallback(array($this, 'triggeredEventCallback')));

		$this->extensionManager = $this->getMock('Tx_Extracache_Configuration_ExtensionManager', array(), array(), '', FALSE);
		$this->extensionManager->expects($this->any())->method('isSupportForFeUsergroupsSet')->will($this->returnValue(TRUE));

		$this->createFileHook = $this->getMock(
			'tx_Extracache_Typo3_Hooks_StaticFileCache_CreateFileHook',
			array('getArgumentRepository', 'getExtensionManager', 'getEventDispatcher', 'isAnonymous', 'isUnprocessibleRequestAction', 'getGetArguments', 'isCrawlerExtensionRunning', 'logMessage')
		);
		$this->createFileHook->expects($this->any())->method('getArgumentRepository')->will($this->returnValue($this->argumentRepository));
		$this->createFileHook->expects($this->any())->method('getEventDispatcher')->will($this->returnValue($this->eventDispatcher));
		$this->createFileHook->expects($this->any())->method('getExtensionManager')->will($this->returnValue($this->extensionManager));
		$this->createFileHook->expects($this->any())->method('isUnprocessibleRequestAction')->will($this->returnValue(FALSE));
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		parent::tearDown();

		unset($this->argumentRepository);
		unset($this->staticFileCache);
		unset($this->frontendUser);
		unset($this->frontend);
		unset($this->createFileHook);
		unset($this->eventDispatcher);
	}

	/**
	 * Tests whether all arguments could be cleaned as defined.
	 *
	 * @return	void
	 * @test
	 */
	public function canCleanAllArgumentsSimple() {
		$uri = 'path-segment/?clean[first]=value&clean[second]=value';

		$parameters = array ('uri' => &$uri, 'TSFE' => $this->frontend);
		$this->createFileHook->initialize($parameters, $this->staticFileCache);

		$this->assertEquals('path-segment/', $uri);
	}

	/**
	 * Tests whether all arguments could not be cleaned since a FOREIGN one appears.
	 *
	 * @return	void
	 * @test
	 */
	public function cannotCleanAllArguments() {
		$uri = 'path-segment/?clean[first]=value&unknown=1&clean[second]=value';

		$parameters = array('uri' => &$uri, 'TSFE' => $this->frontend);
		$this->createFileHook->initialize($parameters, $this->staticFileCache);

		$this->assertEquals('path-segment/?clean[first]=value&unknown=1&clean[second]=value', $uri );
	}

	/**
	 * Tests whether the cache directory contains the frontend user group list.
	 *
	 * @return void
	 * @test
	 */
	public function doesCacheDirectoryContainFrontendUserGroupList() {
		$frontendUserGroupList = '0,-2,3,4,5';
        $this->frontendUser->expects($this->once())->method('getGroupList')->will($this->returnValue($frontendUserGroupList));

		$initialCacheDirectory = $cacheDirectory = uniqid('directory');

		$parameters = array('cacheDir' => &$cacheDirectory, 'TSFE' => $this->frontend);
		$this->createFileHook->initialize($parameters, $this->staticFileCache);

		$this->assertEquals($initialCacheDirectory . '/' . $frontendUserGroupList, $cacheDirectory);
	}

	/**
	 * Tests whether the cache directory contains the frontend user group list.
	 *
	 * @return void
	 * @test
	 */
	public function doesAdditionalHashContainFrontendUserGroupList() {
		$frontendUserGroupList = '0,-2,3,4,5';
        $this->frontendUser->expects($this->once())->method('getGroupList')->will($this->returnValue($frontendUserGroupList));

		$additionalHash = '';

		$parameters = array('additionalHash' => &$additionalHash, 'TSFE' => $this->frontend);
		$this->createFileHook->initialize($parameters, $this->staticFileCache);

		$this->assertEquals(md5($frontendUserGroupList), $additionalHash);
	}

	/**
	 * Tests whether the cache directory contains the frontend user group list.
	 *
	 * @return void
	 * @test
	 */
	public function doFieldValuesContainFrontendUserGroupList() {
		$frontendUserGroupList = '0,-2,3,4,5';
        $this->frontendUser->expects($this->once())->method('getGroupList')->will($this->returnValue($frontendUserGroupList));

		$fieldValues = array();

		$parameters = array('fieldValues' => &$fieldValues, 'TSFE' => $this->frontend);
		$this->createFileHook->initialize($parameters, $this->staticFileCache);

		$this->assertEquals($fieldValues[tx_Extracache_Typo3_Hooks_StaticFileCache_CreateFileHook::FIELD_GroupList], $frontendUserGroupList);
	}

	/**
	 * @return void
	 * @test
	 */
	public function doesHookTryToFixNonSpeakingUris() {
		$this->createFileHook->expects($this->once())->method('isCrawlerExtensionRunning')->will($this->returnValue(FALSE));

		$parameters = array('TSFE' => $this->frontend);
		$this->createFileHook->initialize($parameters, $this->staticFileCache);
	}

	/**
	 * @return void
	 * @test
	 */
	public function isEventTriggeredOnInitializingHook() {
		$parameters = array('TSFE' => $this->frontend);
		$this->createFileHook->initialize($parameters, $this->staticFileCache);

		$this->assertEquals(2, count($this->triggeredEvents));
		$this->assertInstanceOf('Tx_Extracache_System_Event_Events_EventOnStaticFileCache', $this->triggeredEvents[0]);
		$this->assertEquals(tx_Extracache_Typo3_Hooks_StaticFileCache_CreateFileHook::EVENT_PreInitialize, $this->triggeredEvents[0]->getName());
		
		$this->assertInstanceOf('Tx_Extracache_System_Event_Events_EventOnStaticFileCache', $this->triggeredEvents[1]);
		$this->assertEquals(tx_Extracache_Typo3_Hooks_StaticFileCache_CreateFileHook::EVENT_Initialize, $this->triggeredEvents[1]->getName());
	}

	/**
	 * @return void
	 * @test
	 */
	public function doesPageInformationAppearInCachedContent() {
		$this->createFileHook->expects($this->once())->method('getGetArguments')->will($this->returnValue(array('argumentTest' => 1)));

		$config = array('config' => array('configTest' => 1));
		$this->frontend->id = 12345;
		$this->frontend->type = 23456;
		$this->frontend->config = $config;
		$this->frontend->rootLine[0]['uid'] = 34567;
		$parameters = array('TSFE' => $this->frontend);

		$content = $this->createFileHook->process($parameters, $this->staticFileCache);

		$startPosition = strpos($content, Tx_Extracache_System_StaticCache_AbstractManager::DATA_PageInformationPrefix);
		$prefixLength = strlen(Tx_Extracache_System_StaticCache_AbstractManager::DATA_PageInformationPrefix);
		$endPosition = strpos($content, Tx_Extracache_System_StaticCache_AbstractManager::DATA_PageInformationSuffix);
		$pageInformation = unserialize(substr($content, $prefixLength, $endPosition - $prefixLength));

		$this->assertEquals(12345, $pageInformation['id']);
		$this->assertEquals(23456, $pageInformation['type']);
		$this->assertEquals(34567, $pageInformation['firstRootlineId']);
		$this->assertEquals($config, $pageInformation['config']);
		$this->assertEquals(array(), $pageInformation['GET']);
	}

	/**
	 * @return void
	 * @test
	 */
	public function doWhitelistArgumentsAppearInCachedContent() {
		$this->createFileHook->expects($this->once())->method('getGetArguments')->will($this->returnValue(array('argumentTest' => 1, 'whitelist' => 1)));

		$config = array('config' => array('configTest' => 1));
		$this->frontend->id = 12345;
		$this->frontend->type = 23456;
		$this->frontend->config = $config;
		$this->frontend->rootLine[0]['uid'] = 34567;
		$parameters = array('TSFE' => $this->frontend);

		$content = $this->createFileHook->process($parameters, $this->staticFileCache);

		$startPosition = strpos($content, Tx_Extracache_System_StaticCache_AbstractManager::DATA_PageInformationPrefix);
		$prefixLength = strlen(Tx_Extracache_System_StaticCache_AbstractManager::DATA_PageInformationPrefix);
		$endPosition = strpos($content, Tx_Extracache_System_StaticCache_AbstractManager::DATA_PageInformationSuffix);
		$pageInformation = unserialize(substr($content, $prefixLength, $endPosition - $prefixLength));

		$this->assertEquals(12345, $pageInformation['id']);
		$this->assertEquals(23456, $pageInformation['type']);
		$this->assertEquals(34567, $pageInformation['firstRootlineId']);
		$this->assertEquals($config, $pageInformation['config']);
		$this->assertEquals(array('whitelist' => 1), $pageInformation['GET']);
	}

	/**
	 * @return void
	 * @test
	 */
	public function isEventTriggeredOnProcessingCachedContent() {
		$parameters = array('TSFE' => $this->frontend);
		$this->createFileHook->process($parameters, $this->staticFileCache);

		$this->assertEquals(1, count($this->triggeredEvents));
		$this->assertInstanceOf('Tx_Extracache_System_Event_Events_EventOnStaticFileCache', $this->triggeredEvents[0]);
		$this->assertEquals(tx_Extracache_Typo3_Hooks_StaticFileCache_CreateFileHook::EVENT_Process, $this->triggeredEvents[0]->getName());
	}
}