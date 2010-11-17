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
	 * Initializes  controller
	 * @param Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository	$cacheDatabaseEntryRepository
	 * @param Tx_Extracache_Domain_Repository_CacheFileRepository			$cacheFileRepository
	 * @param Tx_Extracache_Configuration_ExtensionManager					$extensionManager
	 * @param Tx_Extracache_View_View $view
	 */
	public function __construct(Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository $cacheDatabaseEntryRepository, Tx_Extracache_Domain_Repository_CacheFileRepository $cacheFileRepository, Tx_Extracache_Configuration_ExtensionManager $extensionManager, Tx_Extracache_View_View $view) {
		$this->setCacheDatabaseEntryRepository( $cacheDatabaseEntryRepository );
		$this->setCacheFileRepository( $cacheFileRepository );
		$this->setExtensionManager( $extensionManager );
		$this->setView( $view );
		$this->getView()->setTemplatePath ( dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'Private' . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR );
		$this->getCacheDatabaseEntryRepository()->setFileTable ( $this->getExtensionManager()->get('fileTable') );
		$this->getCacheFileRepository()->setCacheDir ( PATH_site . $this->getExtensionManager()->get('path_StaticFileCache') );
	}

	/**
	 * @return string
	 */
	public function allDatabaseEntrysAction() {
		try {
			$this->getView()->assign ( 'allDatabaseEntrys', $this->getCacheDatabaseEntryRepository()->getAll () );
			return $this->getView()->render ( 'allDatabaseEntrys' );
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
			$this->getView()->assign ( 'countDatbaseEntrys', $this->getCacheDatabaseEntryRepository()->countAll () );
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
	private function getCacheDatabaseEntryRepository() {
		return $this->cacheDatabaseEntryRepository;
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
	 * @param Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository $cacheDatabaseEntryRepository
	 */
	private function setCacheDatabaseEntryRepository(Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository $cacheDatabaseEntryRepository) {
		$this->cacheDatabaseEntryRepository = $cacheDatabaseEntryRepository;
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