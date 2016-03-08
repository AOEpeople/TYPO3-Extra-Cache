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
 * test case for Tx_Extracache_Configuration_ConfigurationManager
 * @package extracache_tests
 * @subpackage Configuration
 */
class Tx_Extracache_Configuration_ConfigurationManagerTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var Tx_Extracache_Configuration_ConfigurationManager
	 */
	private $manager;
	/**
	 * @var Tx_Extracache_Domain_Repository_ArgumentRepository
	 */
	private $mockedArgumentRepository;
	/**
	 * @var Tx_Extracache_Validation_Validator_Argument
	 */
	private $mockedArgumentValidator;
	/**
	 * @var Tx_Extracache_Domain_Repository_CleanerStrategyRepository
	 */
	private $mockedCleanerStrategyRepository;
	/**
	 * @var Tx_Extracache_Validation_Validator_CleanerStrategy
	 */
	private $mockedCleanerStrategyValidator;
	/**
	 * @var Tx_Extracache_Domain_Repository_ContentProcessorDefinitionRepository
	 */
	private $mockedContentProcessorDefinitionRepository;
	/**
	 * @var Tx_Extracache_Domain_Repository_EventRepository
	 */
	private $mockedEventRepository;
	/**
	 * @var Tx_Extracache_Validation_Validator_Event
	 */
	private $mockedEventValidator;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {

		$this->mockedArgumentRepository = $this->getMock ( 'Tx_Extracache_Domain_Repository_ArgumentRepository', array (), array (), '', FALSE );
		$this->mockedArgumentValidator = $this->getMock ( 'Tx_Extracache_Validation_Validator_Argument', array (), array (), '', FALSE );
		$this->mockedCleanerStrategyRepository = $this->getMock ( 'Tx_Extracache_Domain_Repository_CleanerStrategyRepository', array (), array (), '', FALSE );
		$this->mockedCleanerStrategyValidator = $this->getMock ( 'Tx_Extracache_Validation_Validator_CleanerStrategy', array (), array (), '', FALSE );
		$this->mockedContentProcessorDefinitionRepository = $this->getMock ( 'Tx_Extracache_Domain_Repository_ContentProcessorDefinitionRepository', array (), array (), '', FALSE );
		$this->mockedEventRepository = $this->getMock ( 'Tx_Extracache_Domain_Repository_EventRepository', array (), array (), '', FALSE );
		$this->mockedEventValidator = $this->getMock ( 'Tx_Extracache_Validation_Validator_Event', array (), array (), '', FALSE );

		$this->manager = $this->getMock ( 'Tx_Extracache_Configuration_ConfigurationManager', array ('getArgumentRepository','getArgumentValidator','getCleanerStrategyRepository','getCleanerStrategyValidator','getContentProcessorDefinitionRepository','getEventRepository','getEventValidator'));
		$this->manager->expects ( $this->any () )->method ( 'getArgumentRepository' )->will ( $this->returnValue ( $this->mockedArgumentRepository ) );
		$this->manager->expects ( $this->any () )->method ( 'getArgumentValidator' )->will ( $this->returnValue ( $this->mockedArgumentValidator ) );
		$this->manager->expects ( $this->any () )->method ( 'getCleanerStrategyRepository' )->will ( $this->returnValue ( $this->mockedCleanerStrategyRepository ) );
		$this->manager->expects ( $this->any () )->method ( 'getCleanerStrategyValidator' )->will ( $this->returnValue ( $this->mockedCleanerStrategyValidator ) );
		$this->manager->expects ( $this->any () )->method ( 'getContentProcessorDefinitionRepository' )->will ( $this->returnValue ( $this->mockedContentProcessorDefinitionRepository ) );
		$this->manager->expects ( $this->any () )->method ( 'getEventRepository' )->will ( $this->returnValue ( $this->mockedEventRepository ) );
		$this->manager->expects ( $this->any () )->method ( 'getEventValidator' )->will ( $this->returnValue ( $this->mockedEventValidator ) );
	}

	/**
	 * Test method addArgument
	 * @test
	 */
	public function canAddArgument() {
        $result = $this->getMock ( '\TYPO3\CMS\Extbase\Error\Result', array (), array (), '', FALSE );
        $result->expects($this->once())->method ( 'hasErrors' )->will ($this->returnValue (FALSE));
        $this->mockedArgumentValidator->expects ( $this->once () )->method ( 'validate' )->will ( $this->returnValue ( $result ) );
		$this->mockedArgumentRepository->expects ( $this->once () )->method ( 'addArgument' );
		$this->manager->addArgument('', '', '');
	}
	/**
	 * Test method addArgument
	 * @test
	 * @expectedException RuntimeException
	 */
	public function canNotAddArgument() {
        $error = $this->getMock ( '\TYPO3\CMS\Extbase\Error\Error', array (), array (), '', FALSE );
        $result = $this->getMock ( '\TYPO3\CMS\Extbase\Error\Result', array (), array (), '', FALSE );
        $result->expects($this->once())->method ( 'getErrors' )->will ($this->returnValue (array($error)));
        $result->expects($this->once())->method ( 'hasErrors' )->will ($this->returnValue (TRUE));
        $this->mockedArgumentValidator->expects ( $this->once () )->method ( 'validate' )->will ( $this->returnValue ( $result ) );
        $this->mockedArgumentRepository->expects ( $this->never () )->method ( 'addArgument' );
		$this->manager->addArgument('', '', '');
	}
	/**
	 * Test method addCleanerStrategy
	 * @test
	 */
	public function canAddCleanerStrategy() {
        $result = $this->getMock ( '\TYPO3\CMS\Extbase\Error\Result', array (), array (), '', FALSE );
        $result->expects($this->once())->method ( 'hasErrors' )->will ($this->returnValue (FALSE));
        $this->mockedCleanerStrategyValidator->expects ( $this->once () )->method ( 'validate' )->will ( $this->returnValue ( $result ) );
        $this->mockedCleanerStrategyRepository->expects ( $this->once () )->method ( 'addStrategy' );
        $this->manager->addCleanerStrategy(0, '', '', '', '');
	}
	/**
	 * Test method addCleanerStrategy
	 * @test
	 * @expectedException RuntimeException
	 */
	public function canNotAddCleanerStrategy() {
        $error = $this->getMock ( '\TYPO3\CMS\Extbase\Error\Error', array (), array (), '', FALSE );
        $result = $this->getMock ( '\TYPO3\CMS\Extbase\Error\Result', array (), array (), '', FALSE );
        $result->expects($this->once())->method ( 'getErrors' )->will ($this->returnValue (array($error)));
        $result->expects($this->once())->method ( 'hasErrors' )->will ($this->returnValue (TRUE));
        $this->mockedCleanerStrategyValidator->expects ( $this->once () )->method ( 'validate' )->will ( $this->returnValue ( $result ) );
        $this->mockedCleanerStrategyRepository->expects ( $this->never () )->method ( 'addStrategy' );
        $this->manager->addCleanerStrategy(0, '', '', '', '');
	}
	/**
	 * Test method addContentProcessorDefinition
	 * @test
	 */
	public function canAddContentProcessorDefinition() {
		$this->mockedContentProcessorDefinitionRepository->expects ( $this->once () )->method ( 'addContentProcessorDefinition' );
		$this->manager->addContentProcessorDefinition('dummyClass');
	}
	/**
	 * Test method addEvent
	 * @test
	 */
	public function canAddEvent() {
        $result = $this->getMock ( '\TYPO3\CMS\Extbase\Error\Result', array (), array (), '', FALSE );
        $result->expects($this->once())->method ( 'hasErrors' )->will ($this->returnValue (FALSE));
        $this->mockedEventValidator->expects ( $this->once () )->method ( 'validate' )->will ( $this->returnValue ( $result ) );
        $this->mockedEventRepository->expects ( $this->once () )->method ( 'addEvent' );
		$this->manager->addEvent('');
	}
	/**
	 * Test method addEvent
	 * @test
	 * @expectedException RuntimeException
	 */
	public function canNotAddEvent() {
        $error = $this->getMock ( '\TYPO3\CMS\Extbase\Error\Error', array (), array (), '', FALSE );
        $result = $this->getMock ( '\TYPO3\CMS\Extbase\Error\Result', array (), array (), '', FALSE );
        $result->expects($this->once())->method ( 'getErrors' )->will ($this->returnValue (array($error)));
        $result->expects($this->once())->method ( 'hasErrors' )->will ($this->returnValue (TRUE));
        $this->mockedEventValidator->expects ( $this->once () )->method ( 'validate' )->will ( $this->returnValue ( $result ) );
        $this->mockedEventRepository->expects ( $this->never () )->method ( 'addEvent' );
        $this->manager->addEvent('');
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset( $this->mockedArgumentRepository );
		unset( $this->mockedArgumentValidator );
		unset( $this->mockedCleanerStrategyRepository );
		unset( $this->mockedCleanerStrategyValidator );
		unset( $this->mockedEventRepository );
		unset( $this->mockedEventValidator );
		unset( $this->manager );
	}
}