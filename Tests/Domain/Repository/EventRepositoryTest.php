<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2009 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use \TYPO3\CMS\Core\Utility\GeneralUtility;

require_once dirname ( __FILE__ ) . '/../../AbstractTestcase.php';

/**
 * test case for Tx_Extracache_Domain_Repository_EventRepository
 * @package extracache_tests
 * @subpackage Domain_Repository
 */
class Tx_Extracache_Domain_Repository_EventRepositoryTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 *
	 * @var Tx_Extracache_Domain_Repository_EventRepository
	 */
	private $repository;
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->repository = new Tx_Extracache_Domain_Repository_EventRepository();
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->repository );
	}

	/**
	 * test method addEvent
	 * @test
	 */
	public function addEvent() {
		$event1 = GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_Event', 'key1', 'name1', 0);
		$event2 = GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_Event', 'key2', 'name2', 0);
		$this->assertTrue ( count($this->repository->getEvents ()) === 0 );
		$this->repository->addEvent($event1);
		$this->assertTrue ( count($this->repository->getEvents ()) === 1 );
		$this->repository->addEvent($event2);
		$this->assertTrue ( count($this->repository->getEvents ()) === 2 );
	}
	/**
	 * test method getEvent
	 * @test
	 */
	public function getEvent() {
		$event1 = GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_Event', 'key1', 'name1', 0);
		try {
			$this->repository->getEvent( $event1->getKey() );
			$this->assertTrue ( FALSE, 'method should throw Exception!' );
		} catch(Exception $e) {
			$this->assertInstanceOf ( 'RuntimeException', $e );
		}
		$this->repository->addEvent( $event1 );
		$this->assertEquals( $this->repository->getEvent( $event1->getKey() ), $event1 );
	}
	/**
	 * test method hasEvent
	 * @test
	 */
	public function hasEvent() {
		$event = GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_Event', 'key1', 'name1', 0);
		$this->assertFalse ( $this->repository->hasEvent ('key1') );
		$this->repository->addEvent($event);
		$this->assertTrue ( $this->repository->hasEvent ('key1') );
	}
}