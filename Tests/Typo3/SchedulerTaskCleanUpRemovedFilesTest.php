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
 * test case for Tx_Extracache_Typo3_SchedulerTaskCleanUpRemovedFiles
 * @package extracache_tests
 * @subpackage Typo3
 */
class Tx_Extracache_Typo3_SchedulerTaskCleanUpRemovedFilesTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var Tx_Extracache_System_Event_Dispatcher
	 */
	private $eventDispatcher;
	/**
	 * @var Tx_Extracache_Typo3_SchedulerTaskCleanUpRemovedFiles
	 */
	private $task;
	/**
	 * @var Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	private $typo3DbBackend;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->loadClass('Tx_Extracache_System_Event_Dispatcher');
		$this->loadClass('Tx_Extracache_Typo3_SchedulerTaskCleanUpRemovedFiles');
		$this->loadClass('Tx_Extracache_System_Persistence_Typo3DbBackend');

		$this->eventDispatcher = $this->getMock ( 'Tx_Extracache_System_Event_Dispatcher', array(), array(), '', FALSE);
		$this->typo3DbBackend = $this->getMock ( 'Tx_Extracache_System_Persistence_Typo3DbBackend', array(), array(), '', FALSE);

		$this->task = $this->getMock ( 'Tx_Extracache_Typo3_SchedulerTaskCleanUpRemovedFiles', array('getEventDispatcher', 'getTypo3DbBackend'));
		$this->task->expects($this->any())->method('getEventDispatcher')->will ( $this->returnValue ( $this->eventDispatcher ) );
		$this->task->expects($this->any())->method('getTypo3DbBackend')->will ( $this->returnValue ( $this->typo3DbBackend ) );
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->eventDispatcher );
		unset ( $this->task );
		unset ( $this->typo3DbBackend );
	}

	/**
	 * Test method execute
	 * @test
	 */
	public function execute_withException() {
		$this->typo3DbBackend->expects ( $this->once () )->method ( 'selectQuery' )->will ( $this->throwException(new Exception('') ) );
		$this->eventDispatcher->expects($this->once())->method('triggerEvent');
		$this->assertFalse( $this->task->execute() );
	}
	/**
	 * Test method execute
	 * @test
	 */
	public function execute_withoutException() {
		$row = array();
		$row['id'] = '1';
		$row['tstamp'] = '1111111';
		$row['files'] = 'dummyFile1.txt';
		$rows = array();
		$rows[] = $row;
		$this->typo3DbBackend->expects ( $this->once () )->method ( 'selectQuery' )->will ( $this->returnValue ( $rows ) );
		$this->typo3DbBackend->expects ( $this->once () )->method ( 'deleteQuery' )->with(Tx_Extracache_Typo3_Hooks_FileReferenceModification::TABLE_Queue, 'id=1');
		$this->assertTrue( $this->task->execute() );
	}
}