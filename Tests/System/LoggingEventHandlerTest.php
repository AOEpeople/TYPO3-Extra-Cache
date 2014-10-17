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

require_once dirname ( __FILE__ ) . '/../AbstractTestcase.php';

/**
 * test case for Tx_Extracache_System_LoggingEventHandler
 * @package extracache_tests
 * @subpackage System
 */
class Tx_Extracache_System_LoggingEventHandlerTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var Tx_Extracache_Configuration_ExtensionManager
	 */
	private $extensionManager;
	/**
	 * @var Tx_Extracache_System_LoggingEventHandler
	 */
	private $loggingEventHandler;
	/**
	 * @var array
	 */
	private $messages;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->messages = array();
		$this->extensionManager = $this->getMock ( 'Tx_Extracache_Configuration_ExtensionManager', array (), array (), '', FALSE );
		$this->loggingEventHandler = $this->getMock ( 'Tx_Extracache_System_LoggingEventHandler', array ('getExtensionManager','logMessage'));
		$this->loggingEventHandler->expects ( $this->any () )->method ( 'getExtensionManager' )->will ( $this->returnValue ( $this->extensionManager ) );
		$this->loggingEventHandler->expects($this->any())->method('logMessage')->will($this->returnCallback(array($this, 'loggedMessageCallback')));
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->messages = array();
		unset ( $this->extensionManager );
		unset ( $this->loggingEventHandler );
	}

	/**
	 * Test the method logFatalError
	 * @test
	 */
	public function logFatalError() {
		// message will every time be logged (it doesn't matter if developmentContext is TRUE or FALSE)
		$message = 'test-message';
		$this->extensionManager->expects($this->once())->method('isDevelopmentContextSet')->will($this->returnValue( FALSE ));
		$event = $this->createSimpleEvent( $message );
		$this->loggingEventHandler->logFatalError( $event );
		$this->assertTrue( count($this->messages) === 1 );
		$this->assertTrue( $this->messages[0]['message'] === $message );
		$this->assertTrue( $this->messages[0]['severity'] === Tx_Extracache_System_LoggingEventHandler::LOG_FATAL_ERROR );
	}
	/**
	 * Test the method logInfo
	 * @test
	 */
	public function logInfo() {
		// message will not be logged
		$message = 'test-message';
		$this->extensionManager->expects($this->once())->method('isDevelopmentContextSet')->will($this->returnValue( FALSE ));
		$event = $this->createSimpleEvent( $message );
		$this->loggingEventHandler->logInfo( $event );
		$this->assertTrue( count($this->messages) === 0 );

		// message will be logged
		$this->setUp();
		$message = 'test-message';
		$this->extensionManager->expects($this->once())->method('isDevelopmentContextSet')->will($this->returnValue( TRUE ));
		$event = $this->createSimpleEvent( $message );
		$this->loggingEventHandler->logInfo( $event );
		$this->assertTrue( count($this->messages) === 1 );
		$this->assertTrue( $this->messages[0]['message'] === $message );
		$this->assertTrue( $this->messages[0]['severity'] === Tx_Extracache_System_LoggingEventHandler::LOG_INFO );
	}
	/**
	 * Test the method logNotice
	 * @test
	 */
	public function logNotice() {
		// message will not be logged
		$message = 'test-message';
		$this->extensionManager->expects($this->once())->method('isDevelopmentContextSet')->will($this->returnValue( FALSE ));
		$event = $this->createSimpleEvent( $message );
		$this->loggingEventHandler->logNotice( $event );
		$this->assertTrue( count($this->messages) === 0 );

		// message will be logged
		$this->setUp();
		$message = 'test-message';
		$this->extensionManager->expects($this->once())->method('isDevelopmentContextSet')->will($this->returnValue( TRUE ));
		$event = $this->createSimpleEvent( $message );
		$this->loggingEventHandler->logNotice( $event );
		$this->assertTrue( count($this->messages) === 1 );
		$this->assertTrue( $this->messages[0]['message'] === $message );
		$this->assertTrue( $this->messages[0]['severity'] === Tx_Extracache_System_LoggingEventHandler::LOG_NOTICE );
	}
	/**
	 * Test the method logWarning
	 * @test
	 */
	public function logWarning() {
		// message will every time be logged (it doesn't matter if developmentContext is TRUE or FALSE)
		$message = 'test-message';
		$this->extensionManager->expects($this->once())->method('isDevelopmentContextSet')->will($this->returnValue( FALSE ));
		$event = $this->createSimpleEvent( $message );
		$this->loggingEventHandler->logWarning( $event );
		$this->assertTrue( count($this->messages) === 1 );
		$this->assertTrue( $this->messages[0]['message'] === $message );
		$this->assertTrue( $this->messages[0]['severity'] === Tx_Extracache_System_LoggingEventHandler::LOG_WARNING );
	}

	/**
	 * @param string $message
	 * @param integer $severity
	 */
	public function loggedMessageCallback($message,$severity) {
		$this->messages[] = array('message' => $message, 'severity' => $severity);
	}

	/**
	 * @param	string $message
	 * @return	Tx_Extracache_System_Event_Events_Event
	 */
	private function createSimpleEvent($message) {
		return t3lib_div::makeInstance('Tx_Extracache_System_Event_Events_Event', 'testName', $this, array('message' => $message));
	}
}