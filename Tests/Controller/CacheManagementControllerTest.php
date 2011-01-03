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
	private $cacheDatabaseEntryRepositoryForTableEventlog;
	/**
	 * @var Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository
	 */
	private $cacheDatabaseEntryRepositoryForTableEventqueue;
	/**
	 * @var Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository
	 */
	private $cacheDatabaseEntryRepositoryForTablePages;
	/**
	 * @var Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository
	 */
	private $cacheDatabaseEntryRepositoryForTableStaticCache;
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

		$this->cacheDatabaseEntryRepositoryForTableEventlog = $this->getMock ( 'Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository', array (), array (), '', FALSE );
		$this->cacheDatabaseEntryRepositoryForTableEventqueue = $this->getMock ( 'Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository', array (), array (), '', FALSE );
		$this->cacheDatabaseEntryRepositoryForTablePages = $this->getMock ( 'Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository', array (), array (), '', FALSE );
		$this->cacheDatabaseEntryRepositoryForTableStaticCache = $this->getMock ( 'Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository', array (), array (), '', FALSE );
		$this->cacheFileRepository = $this->getMock ( 'Tx_Extracache_Domain_Repository_CacheFileRepository', array (), array (), '', FALSE );
		$this->extensionManager = $this->getMock ( 'Tx_Extracache_Configuration_ExtensionManager', array (), array (), '', FALSE );
		$this->view = $this->getMock ( 'Tx_Extracache_View_View', array (), array (), '', FALSE );
		$this->cacheManagementController = $this->getMock('Tx_Extracache_Controller_CacheManagementController', array('getModuleData'), array($this->cacheDatabaseEntryRepositoryForTableEventlog, $this->cacheDatabaseEntryRepositoryForTableEventqueue, $this->cacheDatabaseEntryRepositoryForTablePages, $this->cacheDatabaseEntryRepositoryForTableStaticCache, $this->cacheFileRepository, $this->extensionManager, $this->view));
		$this->cacheManagementController->expects($this->any())->method('getModuleData')->will($this->returnCallback(array($this, 'getModuleData')));
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset($this->cacheManagementController);
		unset($this->cacheDatabaseEntryRepositoryForTableEventlog);
		unset($this->cacheDatabaseEntryRepositoryForTableEventqueue);
		unset($this->cacheDatabaseEntryRepositoryForTablePages);
		unset($this->cacheDatabaseEntryRepositoryForTableStaticCache);
		unset($this->cacheFileRepository);
		unset($this->extensionManager);
	}

	/**
	 * @param	string $key
	 * @return	mixed
	 */
	public function getModuleData($key) {
		$value = NULL;
		switch($key) {
			case 'tx_extracache_manager_showFilesDetails':
				$value = TRUE;
			break;
			case 'tx_extracache_manager_showDatabaseDetails':
				$value = TRUE;
			break;
			case 'tx_extracache_manager_searchPhraseForFolders':
				$value = '';
			break;
			case 'tx_extracache_manager_searchPhraseForFiles':
				$value = '';
			break;
			case 'tx_extracache_manager_getFoldersWhichDoesNotContainFiles':
				$value = FALSE;
			break;
		}
		return $value;
	}

	/**
	 * Tests Tx_Extracache_Controller_CacheManagementController->indexAction()
	 * @test
	 */
	public function indexAction() {		
		$this->view->expects($this->once())->method('render');
		$this->cacheDatabaseEntryRepositoryForTableEventlog->expects($this->once())->method('countAll');
		$this->cacheDatabaseEntryRepositoryForTableEventqueue->expects($this->once())->method('countAll');
		$this->cacheDatabaseEntryRepositoryForTablePages->expects($this->once())->method('count');
		$this->cacheDatabaseEntryRepositoryForTableStaticCache->expects($this->once())->method('countAll');
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
	 * Tests Tx_Extracache_Controller_CacheManagementController->allDatabaseEntrysForTableEventlogAction()
	 * @test
	 */
	public function allDatabaseEntrysForTableEventlogAction() {
		$this->view->expects($this->once())->method('render');
		$this->cacheDatabaseEntryRepositoryForTableEventlog->expects($this->once())->method('query');
		$this->cacheManagementController->allDatabaseEntrysForTableEventlogAction ();
	}
	/**
	 * Tests Tx_Extracache_Controller_CacheManagementController->allDatabaseEntrysForTableEventqueueAction()
	 * @test
	 */
	public function allDatabaseEntrysForTableEventqueueAction() {
		$this->view->expects($this->once())->method('render');
		$this->cacheDatabaseEntryRepositoryForTableEventqueue->expects($this->once())->method('getAll');
		$this->cacheManagementController->allDatabaseEntrysForTableEventqueueAction ();
	}
	/**
	 * Tests Tx_Extracache_Controller_CacheManagementController->allDatabaseEntrysForTablePagesAction()
	 * @test
	 */
	public function allDatabaseEntrysForTablePagesAction() {
		$this->view->expects($this->once())->method('render');
		$this->cacheDatabaseEntryRepositoryForTablePages->expects($this->once())->method('query');
		$this->cacheManagementController->allDatabaseEntrysForTablePagesAction ();
	}
	/**
	 * Tests Tx_Extracache_Controller_CacheManagementController->allDatabaseEntrysForTableStaticCacheAction()
	 * @test
	 */
	public function allDatabaseEntrysForTableStaticCacheAction() {
		$this->view->expects($this->once())->method('render');
		$this->cacheDatabaseEntryRepositoryForTableStaticCache->expects($this->once())->method('query');
		$this->cacheManagementController->allDatabaseEntrysForTableStaticCacheAction ();
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