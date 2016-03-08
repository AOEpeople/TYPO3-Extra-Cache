<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2010 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once dirname ( __FILE__ ) . '/Fixtures/ExtendedStaticFileCacheManager.php';

/**
 * Test case for Tx_Extracache_System_StaticCache_StaticFileCacheManager
 *
 * @package extracache_tests
 * @subpackage System_StaticCache
 */
class Tx_Extracache_System_StaticCache_StaticFileCacheManagerTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var ExtendedStaticFileCacheManager
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
		$this->manager = $this->getMock('ExtendedStaticFileCacheManager', array('getArgumentRepository','getFrontendUser'), array($this->mockedEventDispatcher, $this->mockedExtensionManager, $this->mockedStorage, $this->mockedRequest));
		$this->manager->expects($this->any())->method('getArgumentRepository')->will($this->returnValue($this->mockedArgumentRepository));
		$this->manager->expects($this->any())->method('getFrontendUser')->will($this->returnValue($this->mockedFrontendUser));
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
	 * Test Method 'getCachedFolder'
	 * @test
	 */
	public function getCachedFolder() {
		$this->mockedExtensionManager->expects($this->once())->method('get')->with('path_StaticFileCache')->will($this->returnValue('typo3temp/tx_ncstaticfilecache/'));
		$this->assertEquals( $this->manager->getCachedFolder(), 'typo3temp/tx_ncstaticfilecache/' );

		$this->setUp();
		$this->mockedExtensionManager->expects($this->once())->method('get')->with('path_StaticFileCache')->will($this->returnValue('typo3temp/tx_ncstaticfilecache'));
		$this->assertEquals( $this->manager->getCachedFolder(), 'typo3temp/tx_ncstaticfilecache/' );
	}
	/**
	 * Test Method 'getCachedRepresentation'
	 * @test
	 */
	public function getCachedRepresentation_withoutSupportedFeUsergroups() {
		// fileName contains no directory
		$this->mockedExtensionManager->expects($this->once())->method('isSupportForFeUsergroupsSet')->will($this->returnValue(FALSE));
		$this->mockedExtensionManager->expects($this->once())->method('get')->with('path_StaticFileCache')->will($this->returnValue('typo3temp/tx_ncstaticfilecache/'));
		$this->mockedRequest->expects($this->once())->method('getHostName')->will($this->returnValue('www.test-domain.com'));
		$this->mockedRequest->expects($this->once())->method('getFileName')->will($this->returnValue('index.php'));
		$this->mockedFrontendUser->expects($this->never())->method('getGroupList');
		$this->assertEquals( $this->manager->getCachedRepresentation(), 'typo3temp/tx_ncstaticfilecache/www.test-domain.com/index.html' );
		// fileName contains no directory
		$this->setUp();
		$this->mockedExtensionManager->expects($this->once())->method('isSupportForFeUsergroupsSet')->will($this->returnValue(FALSE));
		$this->mockedExtensionManager->expects($this->once())->method('get')->with('path_StaticFileCache')->will($this->returnValue('typo3temp/tx_ncstaticfilecache/'));
		$this->mockedRequest->expects($this->once())->method('getHostName')->will($this->returnValue('www.test-domain.com'));
		$this->mockedRequest->expects($this->once())->method('getFileName')->will($this->returnValue('/index.php'));
		$this->mockedFrontendUser->expects($this->never())->method('getGroupList');
		$this->assertEquals( $this->manager->getCachedRepresentation(), 'typo3temp/tx_ncstaticfilecache/www.test-domain.com/index.html' );

		// fileName contains only a directory
		$this->setUp();
		$this->mockedExtensionManager->expects($this->once())->method('isSupportForFeUsergroupsSet')->will($this->returnValue(FALSE));
		$this->mockedExtensionManager->expects($this->once())->method('get')->with('path_StaticFileCache')->will($this->returnValue('typo3temp/tx_ncstaticfilecache/'));
		$this->mockedRequest->expects($this->once())->method('getHostName')->will($this->returnValue('www.test-domain.com'));
		$this->mockedRequest->expects($this->once())->method('getFileName')->will($this->returnValue('path/'));
		$this->mockedFrontendUser->expects($this->never())->method('getGroupList');
		$this->assertEquals( $this->manager->getCachedRepresentation(), 'typo3temp/tx_ncstaticfilecache/www.test-domain.com/path/index.html' );
		$this->setUp();
		$this->mockedExtensionManager->expects($this->once())->method('isSupportForFeUsergroupsSet')->will($this->returnValue(FALSE));
		$this->mockedExtensionManager->expects($this->once())->method('get')->with('path_StaticFileCache')->will($this->returnValue('typo3temp/tx_ncstaticfilecache/'));
		$this->mockedRequest->expects($this->once())->method('getHostName')->will($this->returnValue('www.test-domain.com'));
		$this->mockedRequest->expects($this->once())->method('getFileName')->will($this->returnValue('path'));
		$this->mockedFrontendUser->expects($this->never())->method('getGroupList');
		$this->assertEquals( $this->manager->getCachedRepresentation(), 'typo3temp/tx_ncstaticfilecache/www.test-domain.com/path/index.html' );
	}
	/**
	 * Test Method 'getCachedRepresentation'
	 * @test
	 */
	public function getCachedRepresentation_withSupportedFeUsergroups() {
		// fileName contains no directory
		$this->mockedExtensionManager->expects($this->once())->method('isSupportForFeUsergroupsSet')->will($this->returnValue(TRUE));
		$this->mockedExtensionManager->expects($this->once())->method('get')->with('path_StaticFileCache')->will($this->returnValue('typo3temp/tx_ncstaticfilecache/'));
		$this->mockedRequest->expects($this->once())->method('getHostName')->will($this->returnValue('www.test-domain.com'));
		$this->mockedRequest->expects($this->once())->method('getFileName')->will($this->returnValue('index.php'));
		$this->mockedFrontendUser->expects($this->once())->method('getGroupList')->will($this->returnValue('0,1'));
		$this->assertEquals( $this->manager->getCachedRepresentation(), 'typo3temp/tx_ncstaticfilecache/www.test-domain.com/0,1/index.html' );
		// fileName contains no directory
		$this->setUp();
		$this->mockedExtensionManager->expects($this->once())->method('isSupportForFeUsergroupsSet')->will($this->returnValue(TRUE));
		$this->mockedExtensionManager->expects($this->once())->method('get')->with('path_StaticFileCache')->will($this->returnValue('typo3temp/tx_ncstaticfilecache/'));
		$this->mockedRequest->expects($this->once())->method('getHostName')->will($this->returnValue('www.test-domain.com'));
		$this->mockedRequest->expects($this->once())->method('getFileName')->will($this->returnValue('/index.php'));
		$this->mockedFrontendUser->expects($this->once())->method('getGroupList')->will($this->returnValue('0,1'));
		$this->assertEquals( $this->manager->getCachedRepresentation(), 'typo3temp/tx_ncstaticfilecache/www.test-domain.com/0,1/index.html' );

		// fileName contains only a directory
		$this->setUp();
		$this->mockedExtensionManager->expects($this->once())->method('isSupportForFeUsergroupsSet')->will($this->returnValue(TRUE));
		$this->mockedExtensionManager->expects($this->once())->method('get')->with('path_StaticFileCache')->will($this->returnValue('typo3temp/tx_ncstaticfilecache/'));
		$this->mockedRequest->expects($this->once())->method('getHostName')->will($this->returnValue('www.test-domain.com'));
		$this->mockedRequest->expects($this->once())->method('getFileName')->will($this->returnValue('path/'));
		$this->mockedFrontendUser->expects($this->once())->method('getGroupList')->will($this->returnValue('0,1'));
		$this->assertEquals( $this->manager->getCachedRepresentation(), 'typo3temp/tx_ncstaticfilecache/www.test-domain.com/0,1/path/index.html' );
		$this->setUp();
		$this->mockedExtensionManager->expects($this->once())->method('isSupportForFeUsergroupsSet')->will($this->returnValue(TRUE));
		$this->mockedExtensionManager->expects($this->once())->method('get')->with('path_StaticFileCache')->will($this->returnValue('typo3temp/tx_ncstaticfilecache/'));
		$this->mockedRequest->expects($this->once())->method('getHostName')->will($this->returnValue('www.test-domain.com'));
		$this->mockedRequest->expects($this->once())->method('getFileName')->will($this->returnValue('path'));
		$this->mockedFrontendUser->expects($this->once())->method('getGroupList')->will($this->returnValue('0,1'));
		$this->assertEquals( $this->manager->getCachedRepresentation(), 'typo3temp/tx_ncstaticfilecache/www.test-domain.com/0,1/path/index.html' );
	}
}