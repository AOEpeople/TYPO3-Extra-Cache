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

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

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
     * @var vfsStreamDirectory
     */
    private $virtualRootDirectory;
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
	    $this->virtualRootDirectory = vfsStream::setup();
        vfsStream::copyFromFileSystem(__DIR__ . '/fixtures/files');

		$this->cacheFileRepository = new Tx_Extracache_Domain_Repository_CacheFileRepository ();
		$this->cacheFileRepository->setCacheDir($this->virtualRootDirectory->url() . '/');
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
		$this->assertEquals(3, $this->cacheFileRepository->countAll());
	}
    /**
     * Tests Method getAllFiles
     * @test
     */
    public function getAllFilesWhenSearchphraseIsEmpty() {
        $results = $this->cacheFileRepository->getAllFiles('');
        $this->assertCount(3, $results);

        foreach ($results as $result) {
            $this->assertInstanceOf('Tx_Extracache_Domain_Model_CacheFile', $result);
            $this->assertContains($result->getName(), array('test.html', 'test/test.html', 'test/test2/test3/test.html'));
        }
    }
	/**
	 * Tests Method getAllFiles
	 * @test
	 */
	public function getAllFilesWhenSearchphraseIsNotEmpty() {
        $results = $this->cacheFileRepository->getAllFiles('test/test');
        $this->assertCount(2, $results);

        foreach ($results as $result) {
            $this->assertInstanceOf('Tx_Extracache_Domain_Model_CacheFile', $result);
            $this->assertContains($result->getName(), array('test/test.html', 'test/test2/test3/test.html'));
        }
    }

    /**
     * Tests Method getAllFolders
     * @test
     */
    public function getAllFoldersWhenSearchphraseIsEmpty() {
        $results = $this->cacheFileRepository->getAllFolders ( TRUE, '' );
        $this->assertCount(3, $results);

        foreach($results as $result){
            $this->assertInstanceOf ( 'Tx_Extracache_Domain_Model_CacheFile', $result );
            $this->assertContains($result->getName(), array('test','test/test2','test/test2/test3'));
        }

        $results = $this->cacheFileRepository->getAllFolders ( FALSE, '' );
        $this->assertCount(2, $results);

        foreach($results as $result){
            $this->assertInstanceOf ( 'Tx_Extracache_Domain_Model_CacheFile', $result );
            $this->assertContains($result->getName(), array('test','test/test2/test3'));
        }
    }
	/**
	 * Tests Method getAllFolders
	 * @test
	 */
	public function getAllFoldersWhenSearchphraseIsNotEmpty() {
		$results = $this->cacheFileRepository->getAllFolders ( TRUE, 'test/test2' );
		$this->assertCount(2, $results);

		foreach($results as $result){
			$this->assertInstanceOf ( 'Tx_Extracache_Domain_Model_CacheFile', $result );
			$this->assertContains($result->getName(), array('test/test2','test/test2/test3'));
		}

		$results = $this->cacheFileRepository->getAllFolders ( FALSE, 'test/test2' );
		$this->assertCount(1, $results);

        foreach($results as $result){
            $this->assertInstanceOf ( 'Tx_Extracache_Domain_Model_CacheFile', $result );
            $this->assertContains($result->getName(), array('test/test2/test3'));
        }
	}
}