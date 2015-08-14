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
require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Classes' . DIRECTORY_SEPARATOR . 'Domain' . DIRECTORY_SEPARATOR . 'Repository' . DIRECTORY_SEPARATOR . 'CacheFileRepository.php';
/**
 * Tx_Extracache_Domain_Repository_CacheFileRepository test case.
 * @package extracache_tests
 * @subpackage Domain_Repository
 */
class Tx_Extracache_Domain_Repository_CacheFileRepositoryTest extends tx_phpunit_testcase {
	/**
	 * @var Tx_Extracache_Domain_Repository_CacheFileRepository
	 */
	private $cacheFileRepository;
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->cacheFileRepository = new Tx_Extracache_Domain_Repository_CacheFileRepository ();
		$this->cacheFileRepository->setCacheDir(dirname(__FILE__).DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR);
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->cacheFileRepository );
	}
	/**
	 * Tests Tx_Extracache_Domain_Repository_CacheFileRepository->countAll()
	 * @test
	 */
	public function countAll() {
		$result = $this->cacheFileRepository->countAll ();
		$this->assertInternalType ( 'integer', $result );
		$this->assertEquals(3, $result);
	}
    /**
     * Tests Method getAllFiles
     * @test
     */
    public function getAllFilesWhenSearchphraseIsEmpty() {
        $this->markTestSkipped('this test is unstable - we need the vfs-stream to mock the filesystem');

        $results = $this->cacheFileRepository->getAllFiles('');
        $this->assertInternalType('array', $results);
        $this->assertEquals(3, count($results));
        foreach ($results as $result) {
            $this->assertInstanceOf('Tx_Extracache_Domain_Model_CacheFile', $result);
            $this->assertTrue(in_array($result->getName(), array('test.html','test/test.html', 'test/test2/test3/test.html')));
        }
    }
	/**
	 * Tests Method getAllFiles
	 * @test
	 */
	public function getAllFilesWhenSearchphraseIsNotEmpty() {
        $this->markTestSkipped('this test is unstable - we need the vfs-stream to mock the filesystem');

        $results = $this->cacheFileRepository->getAllFiles('test/test');
        $this->assertInternalType('array', $results);
        $this->assertEquals(2, count($results));
        foreach ($results as $result) {
            $this->assertInstanceOf('Tx_Extracache_Domain_Model_CacheFile', $result);
            $this->assertTrue(in_array($result->getName(), array('test/test.html', 'test/test2/test3/test.html')));
        }
    }

    /**
     * Tests Method getAllFolders
     * @test
     */
    public function getAllFoldersWhenSearchphraseIsEmpty() {
        $this->markTestSkipped('this test is unstable - we need the vfs-stream to mock the filesystem');

        $results = $this->cacheFileRepository->getAllFolders ( TRUE, '' );
        $this->assertInternalType ( 'array', $results );
        $this->assertEquals(3,count($results));
        foreach($results as $result){
            $this->assertInstanceOf ( 'Tx_Extracache_Domain_Model_CacheFile', $result );
            $this->assertTrue( in_array($result->getName(), array('test','test/test2','test/test2/test3')) );
        }
        $results = $this->cacheFileRepository->getAllFolders ( FALSE, '' );
        $this->assertInternalType ( 'array', $results );
        $this->assertEquals(2,count($results));
        foreach($results as $result){
            $this->assertInstanceOf ( 'Tx_Extracache_Domain_Model_CacheFile', $result );
            $this->assertTrue( in_array($result->getName(), array('test','test/test2/test3')) );
        }
    }
	/**
	 * Tests Method getAllFolders
	 * @test
	 */
	public function getAllFoldersWhenSearchphraseIsNotEmpty() {
        $this->markTestSkipped('this test is unstable - we need the vfs-stream to mock the filesystem');

		$results = $this->cacheFileRepository->getAllFolders ( TRUE, 'test/test2' );
		$this->assertInternalType ( 'array', $results );
		$this->assertEquals(2,count($results));
		foreach($results as $result){
			$this->assertInstanceOf ( 'Tx_Extracache_Domain_Model_CacheFile', $result );
			$this->assertTrue( in_array($result->getName(), array('test/test2','test/test2/test3')) );
		}
		$results = $this->cacheFileRepository->getAllFolders ( FALSE, 'test/test2' );
		$this->assertInternalType ( 'array', $results );
		$this->assertEquals(1,count($results));
        foreach($results as $result){
            $this->assertInstanceOf ( 'Tx_Extracache_Domain_Model_CacheFile', $result );
            $this->assertTrue( in_array($result->getName(), array('test/test2/test3')) );
        }
	}
}