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
require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Domain' . DIRECTORY_SEPARATOR . 'Repository' . DIRECTORY_SEPARATOR . 'CacheDatabaseEntryRepository.php';
require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Domain' . DIRECTORY_SEPARATOR . 'Repository' . DIRECTORY_SEPARATOR . 'CacheFileRepository.php';
require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'View.php';

/**
 * Controller for Cache Management
 * @package extracache
 */
class Tx_Extracache_Controller_CacheManagementController {
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
	 * Initializes  controller
	 * @param Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository	$cacheDatabaseEntryRepositoryForTableEventqueue
	 * @param Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository	$cacheDatabaseEntryRepositoryForTablePages
	 * @param Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository	$cacheDatabaseEntryRepositoryForTableStaticCache
	 * @param Tx_Extracache_Domain_Repository_CacheFileRepository			$cacheFileRepository
	 * @param Tx_Extracache_Configuration_ExtensionManager					$extensionManager
	 * @param Tx_Extracache_View_View $view
	 */
	public function __construct(Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository $cacheDatabaseEntryRepositoryForTableEventqueue, Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository $cacheDatabaseEntryRepositoryForTablePages, Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository $cacheDatabaseEntryRepositoryForTableStaticCache, Tx_Extracache_Domain_Repository_CacheFileRepository $cacheFileRepository, Tx_Extracache_Configuration_ExtensionManager $extensionManager, Tx_Extracache_View_View $view) {
		$this->setCacheDatabaseEntryRepositoryForTableEventqueue( $cacheDatabaseEntryRepositoryForTableEventqueue );
		$this->setCacheDatabaseEntryRepositoryForTablePages( $cacheDatabaseEntryRepositoryForTablePages );
		$this->setCacheDatabaseEntryRepositoryForTableStaticCache( $cacheDatabaseEntryRepositoryForTableStaticCache );
		$this->setCacheFileRepository( $cacheFileRepository );
		$this->setExtensionManager( $extensionManager );
		$this->setView( $view );
		$this->getView()->setTemplatePath ( dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'Private' . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR );
		$this->getCacheDatabaseEntryRepositoryForTableEventqueue()->setFileTable ( 'tx_extracache_eventqueue' );
		$this->getCacheDatabaseEntryRepositoryForTableEventqueue()->setOrderBy( 'first_called_time,status' );
		$this->getCacheDatabaseEntryRepositoryForTablePages()->setFileTable ( 'pages' );
		$this->getCacheDatabaseEntryRepositoryForTablePages()->setOrderBy ( 'title' );
		$this->getCacheDatabaseEntryRepositoryForTableStaticCache()->setFileTable ( $this->getExtensionManager()->get('fileTable') );
		$this->getCacheDatabaseEntryRepositoryForTableStaticCache()->setOrderBy( 'host,uri' );
		$this->getCacheFileRepository()->setCacheDir ( PATH_site . $this->getExtensionManager()->get('path_StaticFileCache') );

		// load translations
		$GLOBALS['LANG']->includeLLFile('EXT:extracache/Resources/Private/Language/locallang.xml');
	}

	/**
	 * show all database-entries of table 'tx_extracache_eventqueue'
	 * 
	 * @return string
	 */
	public function allDatabaseEntrysForTableEventqueueAction() {
		try {
			$this->getView()->assign ( 'allDatabaseEntrysForTableEventqueue', $this->getCacheDatabaseEntryRepositoryForTableEventqueue()->getAll () );
			return $this->getView()->render ( 'allDatabaseEntrysForTableEventqueue' );
		} catch (Exception $e) {
			return $this->showErrorMessage($e);
		}
	}
	/**
	 * show all database-entries of table 'pages', where page-configuration for 'cleanerStrategies' or 'cacheEvents' is set
	 * 
	 * @return string
	 */
	public function allDatabaseEntrysForTablePagesAction() {
		try {
			$this->getView()->assign ( 'allDatabaseEntrysForTablePages', $this->getCacheDatabaseEntryRepositoryForTablePages()->query ( 'tx_extracache_cleanerstrategies!=\'\' OR tx_extracache_events!=\'\'' ) );
			return $this->getView()->render ( 'allDatabaseEntrysForTablePages' );
		} catch (Exception $e) {
			return $this->showErrorMessage($e);
		}
	}
	/**
	 * show all database-entries of staticCache-table
	 * 
	 * @return string
	 */
	public function allDatabaseEntrysForTableStaticCacheAction() {
		try {
			$this->getView()->assign ( 'allDatabaseEntrysForTableStaticCache', $this->getCacheDatabaseEntryRepositoryForTableStaticCache()->getAll () );
			return $this->getView()->render ( 'allDatabaseEntrysForTableStaticCache' );
		} catch (Exception $e) {
			return $this->showErrorMessage($e);
		}
	}
	/**
	 * @return string
	 */
	public function allFilesAction() {
		try {
			$this->getView()->assign ( 'allFiles', $this->getCacheFileRepository()->getAll () );
			return $this->getView()->render ( 'allFiles' );
		} catch (Exception $e) {
			return $this->showErrorMessage($e);
		}
	}
	/**
	 * @return string
	 */
	public function allFoldersAction() {
		try {
			$getFoldersWhichDoesNotContainFiles = $GLOBALS['BE_USER']->getModuleData('tx_extracache_manager_getFoldersWhichDoesNotContainFiles') === FALSE ? FALSE : TRUE;
			$this->getView()->assign ( 'allFolders', $this->getCacheFileRepository()->getAllFolders ( $getFoldersWhichDoesNotContainFiles ) );
			return $this->getView()->render ( 'allFolders' );
		} catch (Exception $e) {
			return $this->showErrorMessage($e);
		}
	}
	/**
	 * @return string
	 */
	public function deleteFileAction() {
		try {
			$this->getCacheFileRepository()->removeFile($_GET['id']);
			return $this->allFilesAction();
		} catch (Exception $e) {
			return $this->showErrorMessage($e);
		}
	}
	/**
	 * @return string
	 */
	public function deleteFolderAction() {
		try {
			$this->getCacheFileRepository()->removeFolder($_GET['id']);
			return $this->allFoldersAction();
		} catch (Exception $e) {
			return $this->showErrorMessage($e);
		}
	}
	/**
	 * Show the count of both repositorys
	 * @return string
	 */
	public function indexAction() {
		try {
			$this->getView()->assign ( 'countFiles', $this->getCacheFileRepository()->countAll () );
			$this->getView()->assign ( 'countDatbaseEntrysForTableEventqueue', $this->getCacheDatabaseEntryRepositoryForTableEventqueue()->countAll () );
			$this->getView()->assign ( 'countDatbaseEntrysForTablePages', $this->getCacheDatabaseEntryRepositoryForTablePages()->count ( 'tx_extracache_cleanerstrategies!=\'\' OR tx_extracache_events!=\'\'' ) );
			$this->getView()->assign ( 'countDatbaseEntrysForTableStaticCache', $this->getCacheDatabaseEntryRepositoryForTableStaticCache()->countAll () );
			$this->getView()->assign ( 'tableEventQueue', $this->getCacheDatabaseEntryRepositoryForTableEventqueue()->getFileTable () );
			$this->getView()->assign ( 'tablePages', $this->getCacheDatabaseEntryRepositoryForTablePages()->getFileTable () );
			$this->getView()->assign ( 'tableStaticCache', $this->getCacheDatabaseEntryRepositoryForTableStaticCache()->getFileTable () );
			return $this->getView()->render ( 'index' );
		} catch (Exception $e) {
			return $this->showErrorMessage($e);
		}	
	}
	/**
	 * @return string
	 */
	public function setConfigGetFoldersWhichDoesNotContainFilesAction() {
		try {
			$GLOBALS['BE_USER']->pushModuleData('tx_extracache_manager_getFoldersWhichDoesNotContainFiles', (boolean) t3lib_div::_GP('getFoldersWhichDoesNotContainFiles'));
			return $this->allFoldersAction();
		} catch (Exception $e) {
			return $this->showErrorMessage($e);
		}
	}

	/**
	 * @return Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository
	 */
	private function getCacheDatabaseEntryRepositoryForTableEventqueue() {
		return $this->cacheDatabaseEntryRepositoryForTableEventqueue;
	}
	/**
	 * @return Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository
	 */
	private function getCacheDatabaseEntryRepositoryForTablePages() {
		return $this->cacheDatabaseEntryRepositoryForTablePages;
	}
	/**
	 * @return Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository
	 */
	private function getCacheDatabaseEntryRepositoryForTableStaticCache() {
		return $this->cacheDatabaseEntryRepositoryForTableStaticCache;
	}
	/**
	 * @return Tx_Extracache_Domain_Repository_CacheFileRepository
	 */
	private function getCacheFileRepository() {
		return $this->cacheFileRepository;
	}
	/**
	 * @return Tx_Extracache_Configuration_ExtensionManager
	 */
	private function getExtensionManager() {
		return $this->extensionManager;
	}
	/**
	 * @return Tx_Extracache_View_View
	 */
	private function getView() {
		return $this->view;
	}

	/**
	 * @param Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository $cacheDatabaseEntryRepositoryForTableEventqueue
	 */
	private function setCacheDatabaseEntryRepositoryForTableEventqueue(Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository $cacheDatabaseEntryRepositoryForTableEventqueue) {
		$this->cacheDatabaseEntryRepositoryForTableEventqueue = $cacheDatabaseEntryRepositoryForTableEventqueue;
	}
	/**
	 * @param Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository $cacheDatabaseEntryRepositoryForTablePages
	 */
	private function setCacheDatabaseEntryRepositoryForTablePages(Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository $cacheDatabaseEntryRepositoryForTablePages) {
		$this->cacheDatabaseEntryRepositoryForTablePages = $cacheDatabaseEntryRepositoryForTablePages;
	}
	/**
	 * @param Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository $cacheDatabaseEntryRepositoryForTableStaticCache
	 */
	private function setCacheDatabaseEntryRepositoryForTableStaticCache(Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository $cacheDatabaseEntryRepositoryForTableStaticCache) {
		$this->cacheDatabaseEntryRepositoryForTableStaticCache = $cacheDatabaseEntryRepositoryForTableStaticCache;
	}
	/**
	 * @param Tx_Extracache_Domain_Repository_CacheFileRepository $cacheFileRepository
	 */
	private function setCacheFileRepository(Tx_Extracache_Domain_Repository_CacheFileRepository $cacheFileRepository) {
		$this->cacheFileRepository = $cacheFileRepository;
	}
	/**
	 * @param Tx_Extracache_Configuration_ExtensionManager $extensionManager
	 */
	private function setExtensionManager(Tx_Extracache_Configuration_ExtensionManager $extensionManager) {
		$this->extensionManager = $extensionManager;
	}
	/**
	 * @param Tx_Extracache_View_View $view
	 */
	private function setView(Tx_Extracache_View_View $view) {
		$this->view = $view;
	}
	/**
	 * @param Exception $exception
	 * @return string
	 */
	private function showErrorMessage(Exception $exception) {
		$this->getView()->assign ( 'errorMessage', $exception->getMessage() );
		return $this->getView()->render ( 'error' );
	}
}