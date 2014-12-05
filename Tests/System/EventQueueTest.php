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

require_once dirname ( __FILE__ ) . '/../AbstractDatabaseTestcase.php';

/**
 * test case for Tx_Extracache_System_EventQueue
 * @package extracache_tests
 * @subpackage System
 */
class Tx_Extracache_System_EventQueueTest extends Tx_Extracache_Tests_AbstractDatabaseTestcase {
	/**
	 * @var array
	 */
	private $recordIds;
	/**
	 * @var Tx_Extracache_System_EventQueue
	 */
	private $queue;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->createQueueObject();
		$this->createTestDB();
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->queue );
		$this->dropDatabase();
	}

	/**
	 * Test Method addEvent
	 * @test
	 */
	public function addEvent_isNecessary() {
		// event is in queue, but with status 'STATUS_InProcess'
		$recordsCount1 = count( $this->recordIds );
		$event = $this->createEvent('testEvent3');
		$this->queue->addEvent( $event );
		$this->determineRecordIds();
		$recordsCount2 = count( $this->recordIds );
		$this->assertEquals($recordsCount1, $recordsCount2-1);

		// event is not in queue
		$recordsCount1 = count( $this->recordIds );
		$event = $this->createEvent('eventIsNotInQueue');
		$this->queue->addEvent( $event );
		$this->determineRecordIds();
		$recordsCount2 = count( $this->recordIds );
		$this->assertEquals($recordsCount1, $recordsCount2-1);
	}
	/**
	 * Test Method addEvent
	 * @test
	 */
	public function addEvent_isNotNecessary() {
		$recordsCount1 = count( $this->recordIds );
		$event = $this->createEvent('testEvent1');
		$this->queue->addEvent( $event );
		$this->determineRecordIds();
		$recordsCount2 = count( $this->recordIds );
		$this->assertEquals($recordsCount1, $recordsCount2);
	}
	/**
	 * Test Method deleteEventKey
	 * @test
	 */
	public function deleteEventKey_whichExistInQueue() {
		// event is in queue with status 'STATUS_WaitForProcessing'
		$recordsCount1 = count( $this->recordIds );
		$this->queue->deleteEventKey( 'testEvent1' );
		$this->determineRecordIds();
		$recordsCount2 = count( $this->recordIds );
		$this->assertEquals($recordsCount1, $recordsCount2);

		// event is in queue with status 'STATUS_InProcess'
		$recordsCount1 = count( $this->recordIds );
		$this->queue->deleteEventKey( 'testEvent3' );
		$this->determineRecordIds();
		$recordsCount2 = count( $this->recordIds );
		$this->assertEquals($recordsCount1, $recordsCount2+1);
	}
	/**
	 * Test Method deleteEventKey
	 * @test
	 */
	public function deleteEventKey_whichNotExistInQueue() {
		$recordsCount1 = count( $this->recordIds );
		$this->queue->deleteEventKey( 'eventIsNotInQueue' );
		$this->determineRecordIds();
		$recordsCount2 = count( $this->recordIds );
		$this->assertEquals($recordsCount1, $recordsCount2);
	}
	/**
	 * Test Method getNextEventKeyForProcessing
	 * @test
	 */
	public function getNextEventKeyForProcessing_returnEvent() {
		$this->queue->expects($this->once())->method('getTime')->will($this->returnValue(2001));
		$this->assertEquals($this->queue->getNextEventKeyForProcessing(), 'testEvent3');
		
		$this->createQueueObject();
		$this->deleteEventFromDb('testEvent3', '1');
		$this->queue->expects($this->once())->method('getTime')->will($this->returnValue(11001));
		$this->assertEquals($this->queue->getNextEventKeyForProcessing(), 'testEvent1');

		$this->createQueueObject();
		$this->deleteEventFromDb('testEvent3', '1');
		$this->deleteEventFromDb('testEvent1', '0');
		$this->deleteEventFromDb('testEvent1', '1');
		$this->queue->expects($this->once())->method('getTime')->will($this->returnValue(12001));
		$this->assertEquals($this->queue->getNextEventKeyForProcessing(), 'testEvent2');
	}	
	/**
	 * Test Method getNextEventKeyForProcessing
	 * @test
	 */
	public function getNextEventKeyForProcessing_returnNull() {
		$this->queue->expects($this->once())->method('getTime')->will($this->returnValue(500));
		$this->assertEquals($this->queue->getNextEventKeyForProcessing(), NULL);

		$this->createQueueObject();
		$this->queue->expects($this->once())->method('getTime')->will($this->returnValue(1500));
		$this->assertEquals($this->queue->getNextEventKeyForProcessing(), NULL);
	}

	/**
	 * @param	string	$key
	 * @param	integer	$interval
	 * @return	Tx_Extracache_Domain_Model_Event
	 */
	private function createEvent($key, $interval=100) {
		return t3lib_div::makeInstance('Tx_Extracache_Domain_Model_Event', $key, '', $interval);
	}
	/**
	 * create queue-object
	 */
	private function createQueueObject() {
		$this->queue = $this->getMock('Tx_Extracache_System_EventQueue', array('getTime'));
	}
	/**
	 * creates the test-database and insert records
	 */
	private function createTestDB() {
		$this->createDatabase();
		$this->useTestDatabase();

		$this->importExtensions(array('extracache'));
		$this->initializeCommonExtensions();
		$this->importDataSet(PATH_tx_extracache . 'Tests/System/Fixtures/TestRecordsForUnittestEventQueue.xml');

		$this->determineRecordIds();
	}
	/**
	 * @param string $eventKey
	 */
	private function deleteEventFromDb($eventKey, $status) {
		global $TYPO3_DB;
		$TYPO3_DB->exec_DELETEquery ( Tx_Extracache_System_EventQueue::TABLE_Queue, 'event_key=\''.$eventKey.'\' AND status='.$status );
	}
	/**
	 * determine record-ID's
	 */
	private function determineRecordIds() {
		global $TYPO3_DB;

		$this->recordIds = array();
		$data = $TYPO3_DB->exec_SELECTgetRows ( 'id', Tx_Extracache_System_EventQueue::TABLE_Queue);
		foreach ($data as $row) {
			$this->recordIds[] = $row['id'];
		}
	}
}