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
require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'AbstractDatabaseTestcase.php';
/**
 * Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository test case.
 * @package extracache_tests
 * @subpackage Domain_Repository
 */
class Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepositoryTest extends Tx_Extracache_Tests_AbstractDatabaseTestcase {
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
		$this->cacheDatabaseEntryRepository->setOrderBy( 'host,uri' );

        $this->createDatabase();
		$this->useTestDatabase();
		$this->importExtensions(array('nc_staticfilecache'));
        $this->importDataSet(PATH_tx_extracache . 'Tests/Domain/Repository/fixtures/db/tx_ncstaticfilecache_file.xml');
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->dropDatabase();
		unset ( $this->cacheDatabaseEntryRepository );
		
	}
	/**
	 * Tests Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository->count()
	 * @test
	 */
	public function canCount() {
		$result = $this->cacheDatabaseEntryRepository->count ('uid=918434');
		$this->assertInternalType ( 'integer', $result );
		$this->assertEquals ( 1, $result );
	}
	/**
	 * Tests Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository->countAll()
	 * @test
	 */
	public function canCountAll() {
		$result = $this->cacheDatabaseEntryRepository->countAll ();
		$this->assertInternalType ( 'integer', $result );
		$this->assertEquals ( 2, $result );
	}
	/**
	 * Tests Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository->getAll()
	 * @test
	 */
	public function canGetAll() {
		$result = $this->cacheDatabaseEntryRepository->getAll ();
		$this->assertInternalType ( 'array', $result );
		$this->assertEquals( count($result), 2 );
		foreach ($result as $item){
			$this->assertInstanceOf ( 'Tx_Extracache_Domain_Model_CacheDatabaseEntry', $item );
		}
	}
	/**
	 * Tests Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository->query()
	 * @test
	 */
	public function canQuery() {
		$result = $this->cacheDatabaseEntryRepository->query ('uid=918434');
		$this->assertInternalType ( 'array', $result );
		$this->assertEquals( count($result), 1 );
		foreach ($result as $item){
			$this->assertInstanceOf ( 'Tx_Extracache_Domain_Model_CacheDatabaseEntry', $item );
		}
	}
}