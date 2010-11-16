<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2010 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once dirname ( __FILE__ ) . '/../../AbstractTestcase.php';

/**
 * Test case for Tx_Extracache_System_StaticCache_Dispatcher
 *
 * @package extracache_tests
 * @subpackage System_StaticCache
 */
class Tx_Extracache_System_StaticCache_DispatcherTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var Tx_Extracache_System_StaticCache_Dispatcher
	 */
	private $dispatcher;

	/**
	 * @var Tx_Extracache_Configuration_ExtensionManager
	 */
	private $extensionManager;

	/**
	 * @var Tx_Extracache_System_Event_Dispatcher
	 */
	private $eventDispatcher;

	/**
	 * @var Tx_Extracache_System_StaticCache_StaticFileCacheManager
	 */
	private $cacheManager;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();

		$this->extensionManager = $this->getMock('Tx_Extracache_Configuration_ExtensionManager', array('get'));

		$this->eventDispatcher = $this->getMock('Tx_Extracache_System_Event_Dispatcher', array('triggerEvent'));
		$this->eventDispatcher->expects($this->any())->method('triggerEvent')->will($this->returnCallback(array($this, 'triggeredEventCallback')));

		$this->cacheManager = $this->getMock(
			'Tx_Extracache_System_StaticCache_StaticFileCacheManager',
			array('isRequestProcessible', 'logForeignArguments', 'loadCachedRepresentation'),
			array(), '', FALSE
		);

		$this->dispatcher = $this->getMock(
			'Tx_Extracache_System_StaticCache_Dispatcher',
			array('isStaticCacheEnabled', 'getCacheManager', 'getExtensionManager', 'getEventDispatcher', 'output', 'halt')
		);
		$this->dispatcher->expects($this->any())->method('halt');
		$this->dispatcher->expects($this->any())->method('getCacheManager')->will($this->returnValue($this->cacheManager));
		$this->dispatcher->expects($this->any())->method('getEventDispatcher')->will($this->returnValue($this->eventDispatcher));
		$this->dispatcher->expects($this->any())->method('getExtensionManager')->will($this->returnValue($this->extensionManager));
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		parent::tearDown();

		unset($this->extensionManager);
		unset($this->eventDispatcher);
		unset($this->cacheManager);
		unset($this->dispatcher);
	}

	/**
	 * Tests whether thrown exceptions are caught.
	 *
	 * @return void
	 * @test
	 */
	public function areExceptionsCaught() {
		$this->dispatcher->expects($this->once())->method('isStaticCacheEnabled')->will ( $this->throwException(new Exception('') ) );
		$this->extensionManager->expects($this->once())->method('get')->with('developmentContext')->will($this->returnValue(0));

		$this->dispatcher->dispatch();

		$this->assertEquals(1, count($this->triggeredEvents));
		$this->assertType('string', $this->triggeredEvents[0]);
		$this->assertEquals('onGeneralFailure', $this->triggeredEvents[0]);
	}

	/**
	 * Tests whether the instance is dispatched.
	 *
	 * @return void
	 * @test
	 */
	public function staticCacheIsNotEnabled() {
		$testContent = uniqid('testContent');

		$this->dispatcher->expects($this->once())->method('isStaticCacheEnabled')->will($this->returnValue(FALSE));
		$this->dispatcher->expects($this->never())->method('output');
		$this->dispatcher->expects($this->never())->method('halt');
		$this->cacheManager->expects($this->never())->method('isRequestProcessible');
		$this->cacheManager->expects($this->never())->method('logForeignArguments');
		$this->cacheManager->expects($this->never())->method('loadCachedRepresentation');

		$this->dispatcher->dispatch();

		$this->assertEquals(0, count($this->triggeredEvents));
	}
	/**
	 * Tests whether the instance is dispatched.
	 *
	 * @return void
	 * @test
	 */
	public function staticCacheIsEnabled_cachedRepresentationIsNotAvailable() {
		$testContent = uniqid('testContent');

		$this->dispatcher->expects($this->once())->method('isStaticCacheEnabled')->will($this->returnValue(TRUE));
		$this->dispatcher->expects($this->never())->method('output');
		$this->dispatcher->expects($this->never())->method('halt');
		$this->cacheManager->expects($this->any())->method('isRequestProcessible')->will($this->returnValue(FALSE));
		$this->cacheManager->expects($this->never())->method('logForeignArguments');
		$this->cacheManager->expects($this->never())->method('loadCachedRepresentation');

		$this->dispatcher->dispatch();

		$this->assertEquals(2, count($this->triggeredEvents));
		$this->assertType('Tx_Extracache_System_Event_Events_EventOnStaticCacheContext', $this->triggeredEvents[0]);
		$this->assertTrue($this->triggeredEvents[0]->getStaticCacheContext());
		$this->assertType('Tx_Extracache_System_Event_Events_EventOnStaticCacheContext', $this->triggeredEvents[1]);
		$this->assertFalse($this->triggeredEvents[1]->getStaticCacheContext());
	}
	/**
	 * Tests whether the instance is dispatched and content is delivered and cached data is available.
	 *
	 * @return void
	 * @test
	 */
	public function staticCacheIsEnabled_cachedRepresentationIsAvailable() {
		$testContent = uniqid('testContent');

		$this->dispatcher->expects($this->once())->method('isStaticCacheEnabled')->will($this->returnValue(TRUE));
		$this->dispatcher->expects($this->once())->method('output')->with($testContent);
		$this->dispatcher->expects($this->once())->method('halt');
		$this->cacheManager->expects($this->any())->method('isRequestProcessible')->will($this->returnValue(TRUE));
		$this->cacheManager->expects($this->once())->method('logForeignArguments');
		$this->cacheManager->expects($this->once())->method('loadCachedRepresentation')->will($this->returnValue($testContent));

		$this->dispatcher->dispatch();

		$this->assertEquals(3, count($this->triggeredEvents));
		$this->assertType('Tx_Extracache_System_Event_Events_EventOnStaticCacheContext', $this->triggeredEvents[0]);
		$this->assertTrue($this->triggeredEvents[0]->getStaticCacheContext());
		$this->assertType('Tx_Extracache_System_Event_Events_EventOnStaticCacheResponsePostProcess', $this->triggeredEvents[1]);
	}
}