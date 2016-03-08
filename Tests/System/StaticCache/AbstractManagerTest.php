<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2010 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once dirname ( __FILE__ ) . '/Fixtures/ExtendedAbstractManager.php';

/**
 * Test case for Tx_Extracache_System_StaticCache_AbstractManager
 *
 * @package extracache_tests
 * @subpackage System_StaticCache
 */
class Tx_Extracache_System_StaticCache_AbstractManagerTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var ExtendedAbstractManager
	 */
	private $manager;
	/**
	 * @var Tx_Extracache_Domain_Repository_ArgumentRepository
	 */
	private $mockedArgumentRepository;
	/**
	 * @var Tx_Extracache_System_Event_Dispatcher
	 */
	private $mockedEventDispatcher;
	/**
	 * @var Tx_Extracache_Configuration_ExtensionManager
	 */
	private $mockedExtensionManager;
	/**
	 * @var Tx_Extracache_Xclass_FrontendUserAuthentication
	 */
	private $mockedFrontendUser;
	/**
	 * @var Tx_Extracache_System_StaticCache_Request
	 */
	private $mockedRequest;
	/**
	 * @var Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	private $mockedStorage;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();

		$this->mockedArgumentRepository = $this->getMock('Tx_Extracache_Domain_Repository_ArgumentRepository', array(), array(), '', FALSE);
		$this->mockedEventDispatcher = $this->getMock('Tx_Extracache_System_Event_Dispatcher', array(), array(), '', FALSE);
		$this->mockedEventDispatcher->expects($this->any())->method('triggerEvent')->will($this->returnCallback(array($this, 'triggeredEventCallback')));
		$this->mockedExtensionManager = $this->getMock('Tx_Extracache_Configuration_ExtensionManager', array(), array(), '', FALSE);
		$this->mockedFrontendUser = $this->getMock('Tx_Extracache_Xclass_FrontendUserAuthentication', array(), array(), '', FALSE);
		$this->mockedRequest = $this->getMock('Tx_Extracache_System_StaticCache_Request', array(), array(), '', FALSE);
		$this->mockedStorage = $this->getMock('Tx_Extracache_System_Persistence_Typo3DbBackend', array(), array(), '', FALSE);
		$this->manager = $this->getMock('ExtendedAbstractManager', array('getArgumentRepository','getCachedRepresentation','getFrontendUser','getFrontendUserWithInitializedFeGroups','isFrontendUserLoggingInOrOut'), array($this->mockedEventDispatcher, $this->mockedExtensionManager, $this->mockedStorage, $this->mockedRequest));
		$this->manager->expects($this->any())->method('getArgumentRepository')->will($this->returnValue($this->mockedArgumentRepository));
		$this->manager->expects($this->any())->method('getFrontendUser')->will($this->returnValue($this->mockedFrontendUser));
        $this->manager->expects($this->any())->method('getFrontendUserWithInitializedFeGroups')->will($this->returnValue($this->mockedFrontendUser));
        $this->manager->expects($this->any())->method('isFrontendUserLoggingInOrOut')->will($this->returnValue(false));
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		parent::tearDown();
		unset($this->manager);
		unset($this->mockedArgumentRepository);
		unset($this->mockedEventDispatcher);
		unset($this->mockedExtensionManager);
		unset($this->mockedFrontendUser);
		unset($this->mockedRequest);
		unset($this->mockedStorage);
	}

	/**
	 * Test Method getCachedRepresentationWithoutPageInformation
	 * @test
	 */
	public function getCachedRepresentationWithoutPageInformation() {
		$content = 'Test-Content :-)';
		$pageInformation = array('uid' => 1, 'name' => 'testPage');
		$cachedContent = Tx_Extracache_System_StaticCache_AbstractManager::DATA_PageInformationPrefix.serialize($pageInformation).Tx_Extracache_System_StaticCache_AbstractManager::DATA_PageInformationSuffix.$content;
		$cachedRepresentation = $this->manager->getCachedRepresentationWithoutPageInformation( $cachedContent );
		$this->assertEquals($cachedRepresentation, $content);
	}
	/**
	 * Test Method getPageInformationFromCachedRepresentation
	 * @test
	 */
	public function getPageInformationFromCachedRepresentation() {
		$content = 'Test-Content :-)';
		$pageInformation = array('uid' => 1, 'name' => 'testPage');
		$cachedContent = Tx_Extracache_System_StaticCache_AbstractManager::DATA_PageInformationPrefix.serialize($pageInformation).Tx_Extracache_System_StaticCache_AbstractManager::DATA_PageInformationSuffix.$content;
		$cachedPageInformation = $this->manager->getPageInformationFromCachedRepresentation( $cachedContent );
		$this->assertEquals($cachedPageInformation, $pageInformation);
	}
	/**
	 * Test Method isRequestProcessible
	 * @test
	 */
	public function isRequestProcessible_staticCacheIsNotAvailable() {
		$this->manager->expects($this->once())->method('getCachedRepresentation')->will($this->returnValue(''));
		$this->assertFalse( $this->manager->isRequestProcessible() );
		$this->assertTrue( count($this->triggeredEvents) === 2 );
		$this->assertInstanceOf( 'Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest', $this->triggeredEvents[0] );
		$this->assertInternalType( 'string', $this->triggeredEvents[1] );
		$this->assertTrue( $this->triggeredEvents[1] === 'onStaticCacheInfo' );
	}
	/**
	 * Test Method isRequestProcessible
	 * @test
	 */
	public function isRequestProcessible_staticCacheIsAvailable() {
		$this->manager->expects($this->once())->method('getCachedRepresentation')->will($this->returnValue( dirname ( __FILE__ ) . '/Fixtures/staticCache.html' ));
		$this->assertTrue( $this->manager->isRequestProcessible() );
		$this->assertTrue( count($this->triggeredEvents) === 1 );
		$this->assertInstanceOf( 'Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest', $this->triggeredEvents[0] );
	}
	/**
	 * Test Method loadCachedRepresentation
	 * @test
	 */
	public function loadCachedRepresentation_staticCacheIsNotAvailable() {
		$this->manager->expects($this->once())->method('getCachedRepresentation')->will($this->returnValue(''));
		$this->assertFalse( $this->manager->loadCachedRepresentation() );
	}
	/**
	 * Test Method loadCachedRepresentation
	 * @test
	 */
	public function loadCachedRepresentation_staticCacheIsAvailable() {
		$this->manager->expects($this->exactly(2))->method('getCachedRepresentation')->will($this->returnValue( dirname ( __FILE__ ) . '/Fixtures/staticCache.html' ));
		$this->assertEquals( $this->manager->loadCachedRepresentation(), 'content from staticCache' );
		$this->assertTrue( count($this->triggeredEvents) === 1 );
		$this->assertTrue( $this->triggeredEvents[0] === 'onStaticCacheLoaded' );
	}
}