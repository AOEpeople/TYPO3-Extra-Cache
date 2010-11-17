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

require_once dirname ( __FILE__ ) . '/../AbstractTestcase.php';

/**
 * Tx_Extracache_Controller_CacheManagementController test case.
 * @package extracache_tests
 * @subpackage Controller
 */
class Tx_Extracache_Controller_CacheManagementControllerTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var Tx_Extracache_Controller_CacheManagementController
	 */
	private $cacheManagementController;
	/**
	 * @var Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository
	 */
	private $cacheDatabaseEntryRepository;
	/**
	 * @var Tx_Extracache_Domain_Repository_CacheFileRepository
	 */
	private $cacheFileRepository;
	/**
	 * @var Tx_Extracache_Configuration_ExtensionManager
	 */
	private $extensionManager;
	/**
	 * @var Tx_Extracache_View_View
	 */
	private $view;
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->loadClass('Tx_Extracache_Controller_CacheManagementController');
		$this->loadClass('Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository');
		$this->loadClass('Tx_Extracache_Domain_Repository_CacheFileRepository');
		$this->loadClass('Tx_Extracache_Configuration_ExtensionManager');
		$this->loadClass('Tx_Extracache_View_View');

		$this->cacheDatabaseEntryRepository = $this->getMock ( 'Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository', array (), array (), '', FALSE );
		$this->cacheFileRepository = $this->getMock ( 'Tx_Extracache_Domain_Repository_CacheFileRepository', array (), array (), '', FALSE );
		$this->extensionManager = $this->getMock ( 'Tx_Extracache_Configuration_ExtensionManager', array (), array (), '', FALSE );
		$this->view = $this->getMock ( 'Tx_Extracache_View_View', array (), array (), '', FALSE );
		$this->cacheManagementController = new Tx_Extracache_Controller_CacheManagementController ( $this->cacheDatabaseEntryRepository, $this->cacheFileRepository, $this->extensionManager, $this->view);
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset($this->cacheManagementController);
		unset($this->cacheDatabaseEntryRepository);
		unset($this->cacheFileRepository);
		unset($this->extensionManager);
	}
	/**
	 * Tests Tx_Extracache_Controller_CacheManagementController->indexAction()
	 * @test
	 */
	public function indexAction() {
		$this->view->expects($this->once())->method('render');
		$this->cacheDatabaseEntryRepository->expects($this->once())->method('countAll');
		$this->cacheFileRepository->expects($this->once())->method('countAll');
		$this->cacheManagementController->indexAction ();
	}
	
	/**
	 * Tests Tx_Extracache_Controller_CacheManagementController->allFilesAction()
	 * @test
	 */
	public function allFilesAction() {
		$this->view->expects($this->once())->method('render');
		$this->cacheFileRepository->expects($this->once())->method('getAll');
		$this->cacheManagementController->allFilesAction ();
	}
	/**
	 * Tests Tx_Extracache_Controller_CacheManagementController->deleteFileAction()
	 * @test
	 */
	public function deleteFileAction() {
		$this->view->expects($this->once())->method('render');
		$this->cacheFileRepository->expects($this->once())->method('removeFile');
		$this->cacheManagementController->deleteFileAction ();
	}
	/**
	 * Tests Tx_Extracache_Controller_CacheManagementController->allDatabaseEntrysAction()
	 * @test
	 */
	public function allDatabaseEntrysAction() {
		$this->view->expects($this->once())->method('render');
		$this->cacheDatabaseEntryRepository->expects($this->once())->method('getAll');
		$this->cacheManagementController->allDatabaseEntrysAction ();
	}
	/**
	 * Tests Tx_Extracache_Controller_CacheManagementController->allFoldersAction()
	 * @test
	 */
	public function allFoldersAction() {
		$this->view->expects($this->once())->method('render');
		$this->cacheFileRepository->expects($this->once())->method('getAllFolders');
		$this->cacheManagementController->allFoldersAction ();
	}
	/**
	 * Tests Tx_Extracache_Controller_CacheManagementController->deleteFolderAction()
	 * @test
	 */
	public function deleteFolderAction() {
		$this->view->expects($this->once())->method('render');
		$this->cacheFileRepository->expects($this->once())->method('removeFolder');
		$this->cacheManagementController->deleteFolderAction ();
	}
}