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

require_once dirname ( __FILE__ ) . '/../../AbstractTestcase.php';

/**
 * test case for Tx_Extracache_Domain_Repository_ContentProcessorDefinitionRepository
 * @package extracache_tests
 * @subpackage Domain_Repository
 */
class Tx_Extracache_Domain_Repository_ContentProcessorDefinitionRepositoryTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * 
	 * @var Tx_Extracache_Domain_Repository_ContentProcessorDefinitionRepository
	 */
	private $repository;
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->loadClass('Tx_Extracache_Domain_Model_ContentProcessorDefinition');
		$this->loadClass('Tx_Extracache_Domain_Repository_ContentProcessorDefinitionRepository');
		$this->repository = new Tx_Extracache_Domain_Repository_ContentProcessorDefinitionRepository();
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->repository );
	}

	/**
	 * test method addContentProcessorDefinition
	 * @test
	 */
	public function addContentProcessorDefinition() {
		$contentProcessorDefinition = t3lib_div::makeInstance('Tx_Extracache_Domain_Model_ContentProcessorDefinition', 'dummyClassName', '/dummy/path/');
		$this->assertTrue ( count($this->repository->getContentProcessorDefinitions ()) === 0 );
		$this->repository->addContentProcessorDefinition($contentProcessorDefinition);
		$this->assertTrue ( count($this->repository->getContentProcessorDefinitions ()) === 1 );
	}
}