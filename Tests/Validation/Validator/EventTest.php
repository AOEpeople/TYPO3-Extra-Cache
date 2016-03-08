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

/**
 * test case for Tx_Extracache_Validation_Validator_Event
 * @package extracache_tests
 * @subpackage Validation_Validator
 */
class Tx_Extracache_Validation_Validator_EventTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * 
	 * @var Tx_Extracache_Validation_Validator_Event
	 */
	private $validator;
	/**
	 * 
	 * @var Tx_Extracache_Domain_Repository_EventRepository
	 */
	private $mockedEventRepository;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->mockedEventRepository = $this->getMock ( 'Tx_Extracache_Domain_Repository_EventRepository', array (), array (), '', FALSE );
		$this->validator = $this->getMock ( 'Tx_Extracache_Validation_Validator_Event', array ('getEventRepository'));
		$this->validator->expects ( $this->any () )->method ( 'getEventRepository' )->will ( $this->returnValue ( $this->mockedEventRepository ) );
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->validator );
	}

	/**
	 * test method isValid
	 * @test
	 */
	public function eventIsValid() {
		$this->mockedEventRepository->expects ( $this->once () )->method ( 'hasEvent' )->will ( $this->returnValue ( FALSE ) );
        $event = $this->createEvent(0);
		$this->assertFalse($this->validator->validate($event)->hasErrors());
	}
	/**
	 * test method isValid
	 * @test
	 */
	public function eventIsNotValid() {
		$this->mockedEventRepository->expects ( $this->once () )->method ( 'hasEvent' )->will ( $this->returnValue ( TRUE ) );
        $event = $this->createEvent(0);
        $this->assertTrue($this->validator->validate($event)->hasErrors());

		$this->setUp();
		$this->mockedEventRepository->expects ( $this->once () )->method ( 'hasEvent' )->will ( $this->returnValue ( FALSE ) );
        $event = $this->createEvent(-1);
        $this->assertTrue($this->validator->validate($event)->hasErrors());
	}

	/**
	 * @param integer $interval
	 * @return Tx_Extracache_Domain_Model_Event
	 */
	private function createEvent($interval) {
		return new Tx_Extracache_Domain_Model_Event('testkey', 'testname', $interval, FALSE);
	}
}