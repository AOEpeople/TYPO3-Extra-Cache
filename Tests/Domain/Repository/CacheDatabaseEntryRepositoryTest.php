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
require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..'  . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Classes' . DIRECTORY_SEPARATOR . 'Domain' . DIRECTORY_SEPARATOR . 'Repository' . DIRECTORY_SEPARATOR . 'CacheDatabaseEntryRepository.php';
/**
 * Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository test case.
 * @package extracache
 */
class Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepositoryTest extends tx_phpunit_database_testcase {
	/**
	 * @var Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository
	 */
	private $cacheDatabaseEntryRepository;
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->cacheDatabaseEntryRepository = new Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository ();
		$this->cacheDatabaseEntryRepository->setFileTable('tx_ncstaticfilecache_file');
		$this->assertTrue($this->createDatabase());
		$this->useTestDatabase();
		$this->importExtensions(array('nc_staticfilecache'));
		$path = dirname ( __FILE__ ) . DIRECTORY_SEPARATOR .'fixtures'.DIRECTORY_SEPARATOR.'db'.DIRECTORY_SEPARATOR.'tx_ncstaticfilecache_file.xml';
		$this->importDataSet($path);
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->dropDatabase();
		unset ( $this->cacheDatabaseEntryRepository );
		
	}
	/**
	 * Tests Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository->countAll()
	 * @test
	 */
	public function countAll() {
		$result = $this->cacheDatabaseEntryRepository->countAll ();
		$this->assertType ( 'integer', $result );
		$this->assertEquals ( 2, $result );
	}
	/**
	 * Tests Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository->getAll()
	 * @test
	 */
	public function getAll() {
		$result = $this->cacheDatabaseEntryRepository->getAll ();
		$this->assertType ( 'array', $result );
		foreach ($result as $item){
			$this->assertType ( 'Tx_Extracache_Domain_Model_CacheDatabaseEntry', $item );
		}
	}
}