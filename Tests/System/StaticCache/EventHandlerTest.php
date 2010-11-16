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
 * Test case for Tx_Extracache_System_StaticCache_EventHandler
 *
 * @package extracache_tests
 * @subpackage System_StaticCache
 */
class Tx_Extracache_System_StaticCache_EventHandlerTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var Tx_Extracache_Domain_Repository_ArgumentRepository
	 */
	private $argumentRepository;
	/**
	 * @var Tx_Extracache_System_Event_Dispatcher
	 */
	private $dispatcher;
	/**
	 * @var Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest
	 */
	private $eventOnStaticCacheRequest;
	/**
	 * @var tslib_feUserAuth
	 */
	private $frontendUser;
	/**
	 * @var Tx_Extracache_System_StaticCache_EventHandler
	 */
	private $eventHandler;
	/**
	 * @var Tx_Extracache_System_StaticCache_Request
	 */
	private $request;
	/**
	 * @var Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	private $storage;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->argumentRepository = $this->getMock('Tx_Extracache_Domain_Repository_ArgumentRepository', array(), array(), '', FALSE);
		$this->dispatcher = $this->getMock('Tx_Extracache_System_Event_Dispatcher', array(), array(), '', FALSE);
		$this->storage = $this->getMock('Tx_Extracache_System_Persistence_Typo3DbBackend', array(), array(), '', FALSE);
		$this->eventHandler = $this->getMock('Tx_Extracache_System_StaticCache_EventHandler', array('getArgumentRepository', 'getCheckMethods', 'getDispatcher', 'getStorage'));
		$this->eventHandler->expects($this->any())->method('getArgumentRepository')->will($this->returnValue($this->argumentRepository));
		$this->eventHandler->expects($this->any())->method('getDispatcher')->will($this->returnValue($this->dispatcher));
		$this->eventHandler->expects($this->any())->method('getStorage')->will($this->returnValue($this->storage));

		$this->frontendUser = $this->getMock('tslib_feUserAuth', array(), array(), '', FALSE);
		$this->request = $this->getMock('Tx_Extracache_System_StaticCache_Request', array(), array(), '', FALSE);
		$this->eventOnStaticCacheRequest = $this->getMock('Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest', array('getFrontendUser', 'getRequest'));
		$this->eventOnStaticCacheRequest->expects($this->any())->method('getFrontendUser')->will($this->returnValue($this->frontendUser));
		$this->eventOnStaticCacheRequest->expects($this->any())->method('getRequest')->will($this->returnValue($this->request));
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset($this->argumentRepository);
		unset($this->dispatcher);
		unset($this->storage);
		unset($this->eventHandler);
	}

	/**
	 * @test
	 */
	public function handleEventOnStaticCacheRequest_isBackendUserActive() {
		// be_user is not active
		$this->eventHandler->expects($this->any())->method('getCheckMethods')->will($this->returnValue( array('isBackendUserActive' => FALSE)));
		$this->request->expects ( $this->once () )->method ( 'getCookie' )->will ( $this->returnValue ( NULL ) );
		$this->eventHandler->handleEventOnStaticCacheRequest( $this->eventOnStaticCacheRequest );
		$this->assertFalse( $this->eventOnStaticCacheRequest->isCanceled() );
	}
	/**
	 * @test
	 */
	public function handleEventOnStaticCacheRequest_isCrawlerExtensionRunning() {
		// crawler is not running
		$GLOBALS ['TYPO3_LOADED_EXT'] ['crawler'] = true;
		$this->eventHandler->expects($this->any())->method('getCheckMethods')->will($this->returnValue( array('isCrawlerExtensionRunning' => FALSE)));
		$this->request->expects ( $this->once () )->method ( 'getServerVariable' )->with ( 'HTTP_X_T3CRAWLER' )->will ( $this->returnValue ( NULL ) );
		$this->storage->expects ( $this->never () )->method ( 'selectQuery' );
		$this->eventHandler->handleEventOnStaticCacheRequest( $this->eventOnStaticCacheRequest );
		$this->assertFalse( $this->eventOnStaticCacheRequest->isCanceled() );

		// crawler is running
		$this->setUp();
		$GLOBALS ['TYPO3_LOADED_EXT'] ['crawler'] = true;
		$record = array();
		$record['qid'] = 11;
		$record['set_id'] = 12;
		$records = array();
		$records[] = $record;
		$crawlerQueueId = 12345;
		$header = $crawlerQueueId.':'.md5($record[0]['qid'].'|'.$record[0]['set_id'].'|'.$GLOBALS ['TYPO3_CONF_VARS'] ['SYS'] ['encryptionKey']);
		$this->eventHandler->expects($this->any())->method('getCheckMethods')->will($this->returnValue( array('isCrawlerExtensionRunning' => FALSE)));
		$this->request->expects ( $this->once () )->method ( 'getServerVariable' )->with ( 'HTTP_X_T3CRAWLER' )->will ( $this->returnValue ( $header ) );
		$this->storage->expects ( $this->once () )->method ( 'selectQuery' )->with('qid,set_id','tx_crawler_queue','qid='.$crawlerQueueId)->will ( $this->returnValue ( $records ) );
		$this->eventHandler->handleEventOnStaticCacheRequest( $this->eventOnStaticCacheRequest );
		$this->assertFalse( $this->eventOnStaticCacheRequest->isCanceled() );
	}
	/**
	 * @test
	 */
	public function handleEventOnStaticCacheRequest_isFrontendUserActive() {
		// fe_user is not active
		$this->eventHandler->expects($this->any())->method('getCheckMethods')->will($this->returnValue( array('isFrontendUserActive' => FALSE)));
		$this->frontendUser->user = array();
		$this->eventHandler->handleEventOnStaticCacheRequest( $this->eventOnStaticCacheRequest );
		$this->assertFalse( $this->eventOnStaticCacheRequest->isCanceled() );

		// fe_user is active
		$this->setUp();
		$this->eventHandler->expects($this->any())->method('getCheckMethods')->will($this->returnValue( array('isFrontendUserActive' => FALSE)));
		$user = array();
		$user['uid'] = 5;
		$this->frontendUser->user = $user;
		$this->eventHandler->handleEventOnStaticCacheRequest( $this->eventOnStaticCacheRequest );
		$this->assertTrue( $this->eventOnStaticCacheRequest->isCanceled() );
	}
	/**
	 * @test
	 */
	public function handleEventOnStaticCacheRequest_isFrontendUserLoggingIn() {
		// fe_user is not logging in
		$this->eventHandler->expects($this->any())->method('getCheckMethods')->will($this->returnValue( array('isFrontendUserLoggingIn' => FALSE)));
		$loginData = array();
		$this->frontendUser->expects ( $this->once () )->method ( 'getLoginFormData' )->will ( $this->returnValue ( $loginData ) );
		$this->eventHandler->handleEventOnStaticCacheRequest( $this->eventOnStaticCacheRequest );
		$this->assertFalse( $this->eventOnStaticCacheRequest->isCanceled() );

		// fe_user is logging in
		$this->setUp();
		$this->eventHandler->expects($this->any())->method('getCheckMethods')->will($this->returnValue( array('isFrontendUserLoggingIn' => FALSE)));
		$loginData = array('uident' => 'test', 'status' => 'login');
		$this->frontendUser->expects ( $this->once () )->method ( 'getLoginFormData' )->will ( $this->returnValue ( $loginData ) );
		$this->eventHandler->handleEventOnStaticCacheRequest( $this->eventOnStaticCacheRequest );
		$this->assertTrue( $this->eventOnStaticCacheRequest->isCanceled() );
	}
	/**
	 * @test
	 */
	public function handleEventOnStaticCacheRequest_isFrontendUserLoggingOut() {
		// fe_user is not logging out
		$this->eventHandler->expects($this->any())->method('getCheckMethods')->will($this->returnValue( array('isFrontendUserLoggingOut' => FALSE)));
		$loginData = array();
		$this->frontendUser->expects ( $this->once () )->method ( 'getLoginFormData' )->will ( $this->returnValue ( $loginData ) );
		$this->eventHandler->handleEventOnStaticCacheRequest( $this->eventOnStaticCacheRequest );
		$this->assertFalse( $this->eventOnStaticCacheRequest->isCanceled() );

		// fe_user is logging out
		$this->setUp();
		$this->eventHandler->expects($this->any())->method('getCheckMethods')->will($this->returnValue( array('isFrontendUserLoggingOut' => FALSE)));
		$loginData = array('status' => 'logout');
		$this->frontendUser->expects ( $this->once () )->method ( 'getLoginFormData' )->will ( $this->returnValue ( $loginData ) );
		$this->eventHandler->handleEventOnStaticCacheRequest( $this->eventOnStaticCacheRequest );
		$this->assertTrue( $this->eventOnStaticCacheRequest->isCanceled() );
	}
	/**
	 * @test
	 */
	public function handleEventOnStaticCacheRequest_isPageMailerExtensionRunning() {
		// pageMailer is not running
		$GLOBALS ['TYPO3_LOADED_EXT'] ['aoe_pagemailer'] = true;
		$this->eventHandler->expects($this->any())->method('getCheckMethods')->will($this->returnValue( array('isPageMailerExtensionRunning' => FALSE)));
		$this->request->expects ( $this->once () )->method ( 'getServerVariable' )->with ( 'HTTP_X_PAGEMAILER' )->will ( $this->returnValue ( NULL ) );
		$this->eventHandler->handleEventOnStaticCacheRequest( $this->eventOnStaticCacheRequest );
		$this->assertFalse( $this->eventOnStaticCacheRequest->isCanceled() );

		// pageMailer is running
		$this->setUp();
		$GLOBALS ['TYPO3_LOADED_EXT'] ['aoe_pagemailer'] = true;
		$this->eventHandler->expects($this->any())->method('getCheckMethods')->will($this->returnValue( array('isPageMailerExtensionRunning' => FALSE)));
		$this->request->expects ( $this->once () )->method ( 'getServerVariable' )->with ( 'HTTP_X_PAGEMAILER' )->will ( $this->returnValue ( 1 ) );
		$this->eventHandler->handleEventOnStaticCacheRequest( $this->eventOnStaticCacheRequest );
		$this->assertTrue( $this->eventOnStaticCacheRequest->isCanceled() );
	}
	/**
	 * @test
	 */
	public function handleEventOnStaticCacheRequest_isProcessingDirtyPages() {
		// process 'dirtyPages' is not running
		$this->eventHandler->expects($this->any())->method('getCheckMethods')->will($this->returnValue( array('isProcessingDirtyPages' => FALSE)));
		$this->request->expects ( $this->once () )->method ( 'getServerVariable' )->with ( 'HTTP_X_PROCESS_DIRTY_PAGES' )->will ( $this->returnValue ( NULL ) );
		$this->eventHandler->handleEventOnStaticCacheRequest( $this->eventOnStaticCacheRequest );
		$this->assertFalse( $this->eventOnStaticCacheRequest->isCanceled() );

		// process 'dirtyPages' is running
		$this->setUp();
		$this->eventHandler->expects($this->any())->method('getCheckMethods')->will($this->returnValue( array('isProcessingDirtyPages' => FALSE)));
		$this->request->expects ( $this->once () )->method ( 'getServerVariable' )->with ( 'HTTP_X_PROCESS_DIRTY_PAGES' )->will ( $this->returnValue ( 1 ) );
		$this->eventHandler->handleEventOnStaticCacheRequest( $this->eventOnStaticCacheRequest );
		$this->assertTrue( $this->eventOnStaticCacheRequest->isCanceled() );
	}
	/**
	 * @test
	 */
	public function handleEventOnStaticCacheRequest_isUnprocessibleRequestAction() {
		// request is processible
		$requestIsProcessibleTest1 = array();
		$requestIsProcessibleTest1['definedArguments'] = array( 'order' => 'value' );
		$requestIsProcessibleTest1['unprocessibleArguments'] = array('basket' => true);
		$requestIsProcessibleTest2 = array();
		$requestIsProcessibleTest2['definedArguments'] = array( 'action' => 'show' );
		$requestIsProcessibleTest2['unprocessibleArguments'] = array('action' => 'delete');
		$requestIsProcessibleTest3 = array();
		$requestIsProcessibleTest3['definedArguments'] = array( 'action' => 'show' );
		$requestIsProcessibleTest3['unprocessibleArguments'] = array('*' => array('action' => array('insert', 'update')));
		$requestIsProcessibleTest4 = array();
		$requestIsProcessibleTest4['definedArguments'] = array( 'basket' => array('action' => 'show') );
		$requestIsProcessibleTest4['unprocessibleArguments'] = array('*' => array('action' => array('insert', 'update')));
		$requestIsProcessibleTest5 = array();
		$requestIsProcessibleTest5['definedArguments'] = array( 'basket' => 'value' );
		$requestIsProcessibleTest5['unprocessibleArguments'] = array('basket' => array('action' => array('insert', 'update')));
		$requestIsProcessibleTest6 = array();
		$requestIsProcessibleTest6['definedArguments'] = array( 'basket' => array('action' => 'show') );
		$requestIsProcessibleTest6['unprocessibleArguments'] = array('basket' => array('action' => array('insert', 'update')));
		$requestIsProcessibleTests = array();
		$requestIsProcessibleTests[] = $requestIsProcessibleTest1;
		$requestIsProcessibleTests[] = $requestIsProcessibleTest2;
		$requestIsProcessibleTests[] = $requestIsProcessibleTest3;
		$requestIsProcessibleTests[] = $requestIsProcessibleTest4;
		$requestIsProcessibleTests[] = $requestIsProcessibleTest5;
		$requestIsProcessibleTests[] = $requestIsProcessibleTest6;
		foreach($requestIsProcessibleTests as $requestIsProcessibleTest) {
			$this->setUp();
			$this->eventHandler->expects($this->any())->method('getCheckMethods')->will($this->returnValue( array('isUnprocessibleRequestAction' => FALSE)));
			$this->request->expects ( $this->once () )->method ( 'getArguments' )->will ( $this->returnValue ( $requestIsProcessibleTest['definedArguments'] ) );
			$unProcessibleArguments = $this->createArgumentObjects($requestIsProcessibleTest['unprocessibleArguments']);
			$this->argumentRepository->expects ( $this->once () )->method ( 'getArgumentsByType' )->with ( Tx_Extracache_Domain_Model_Argument::TYPE_unprocessible )->will ( $this->returnValue ( $unProcessibleArguments ) );
			$this->eventHandler->handleEventOnStaticCacheRequest( $this->eventOnStaticCacheRequest );
			$this->assertFalse( $this->eventOnStaticCacheRequest->isCanceled() );
		}

		// request is not processible
		$requestIsProcessibleTest1 = array();
		$requestIsProcessibleTest1['definedArguments'] = array( 'basket' => 'delete' );
		$requestIsProcessibleTest1['unprocessibleArguments'] = array('basket' => true);
		$requestIsProcessibleTest2 = array();
		$requestIsProcessibleTest2['definedArguments'] = array( 'basket' => array('action' => 'delete') );
		$requestIsProcessibleTest2['unprocessibleArguments'] = array('basket' => true);
		$requestIsProcessibleTest3 = array();
		$requestIsProcessibleTest3['definedArguments'] = array( 'action' => 'delete' );
		$requestIsProcessibleTest3['unprocessibleArguments'] = array('action' => 'delete');
		$requestIsProcessibleTest4 = array();
		$requestIsProcessibleTest4['definedArguments'] = array( 'basket' => array('action' => 'insert') );
		$requestIsProcessibleTest4['unprocessibleArguments'] = array('*' => array('action' => array('insert', 'update')));
		$requestIsProcessibleTest5 = array();
		$requestIsProcessibleTest5['definedArguments'] = array( 'basket' => array('action' => 'insert') );
		$requestIsProcessibleTest5['unprocessibleArguments'] = array('basket' => array('action' => array('insert', 'update')));
		$requestIsProcessibleTests = array();
		$requestIsProcessibleTests[] = $requestIsProcessibleTest1;
		$requestIsProcessibleTests[] = $requestIsProcessibleTest2;
		$requestIsProcessibleTests[] = $requestIsProcessibleTest3;
		$requestIsProcessibleTests[] = $requestIsProcessibleTest4;
		$requestIsProcessibleTests[] = $requestIsProcessibleTest5;
		foreach($requestIsProcessibleTests as $requestIsProcessibleTest) {
			$this->setUp();
			$this->eventHandler->expects($this->any())->method('getCheckMethods')->will($this->returnValue( array('isUnprocessibleRequestAction' => FALSE)));
			$this->request->expects ( $this->once () )->method ( 'getArguments' )->will ( $this->returnValue ( $requestIsProcessibleTest['definedArguments'] ) );
			$unProcessibleArguments = $this->createArgumentObjects($requestIsProcessibleTest['unprocessibleArguments']);
			$this->argumentRepository->expects ( $this->once () )->method ( 'getArgumentsByType' )->with ( Tx_Extracache_Domain_Model_Argument::TYPE_unprocessible )->will ( $this->returnValue ( $unProcessibleArguments ) );
			$this->eventHandler->handleEventOnStaticCacheRequest( $this->eventOnStaticCacheRequest );
			$this->assertTrue( $this->eventOnStaticCacheRequest->isCanceled() );
		}
	}

	/**
	 * @param	array $configurationValue
	 * @return	array
	 */
	private function createArgumentObjects(array $argumentsConfig) {
		$arguments = array();
		foreach($argumentsConfig as $name => $value) {
			$arguments[] = t3lib_div::makeInstance('Tx_Extracache_Domain_Model_Argument', $name, Tx_Extracache_Domain_Model_Argument::TYPE_unprocessible, $value);
		}
		return $arguments;
	}
}