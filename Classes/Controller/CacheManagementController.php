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
	 * Initializes  controller
	 * @param Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository	$cacheDatabaseEntryRepositoryForTableEventlog
	 * @param Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository	$cacheDatabaseEntryRepositoryForTableEventqueue
	 * @param Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository	$cacheDatabaseEntryRepositoryForTablePages
	 * @param Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository	$cacheDatabaseEntryRepositoryForTableStaticCache
	 * @param Tx_Extracache_Domain_Repository_CacheFileRepository			$cacheFileRepository
	 * @param Tx_Extracache_Configuration_ExtensionManager					$extensionManager
	 * @param Tx_Extracache_View_View $view
	 */
	public function __construct(Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository $cacheDatabaseEntryRepositoryForTableEventlog, Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository $cacheDatabaseEntryRepositoryForTableEventqueue, Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository $cacheDatabaseEntryRepositoryForTablePages, Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository $cacheDatabaseEntryRepositoryForTableStaticCache, Tx_Extracache_Domain_Repository_CacheFileRepository $cacheFileRepository, Tx_Extracache_Configuration_ExtensionManager $extensionManager, Tx_Extracache_View_View $view) {
		$this->setCacheDatabaseEntryRepositoryForTableEventlog( $cacheDatabaseEntryRepositoryForTableEventlog );
		$this->setCacheDatabaseEntryRepositoryForTableEventqueue( $cacheDatabaseEntryRepositoryForTableEventqueue );
		$this->setCacheDatabaseEntryRepositoryForTablePages( $cacheDatabaseEntryRepositoryForTablePages );
		$this->setCacheDatabaseEntryRepositoryForTableStaticCache( $cacheDatabaseEntryRepositoryForTableStaticCache );
		$this->setCacheFileRepository( $cacheFileRepository );
		$this->setExtensionManager( $extensionManager );
		$this->setView( $view );
		$this->getView()->setTemplatePath ( dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'Private' . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR );

		$this->getCacheDatabaseEntryRepositoryForTableEventlog()->setFileTable ( 'tx_extracache_eventlog' );
		$this->getCacheDatabaseEntryRepositoryForTableEventlog()->setFieldForCountOperation( 'id' );
		$this->getCacheDatabaseEntryRepositoryForTableEventlog()->setOrderBy( 'start_time DESC' );
		$this->getCacheDatabaseEntryRepositoryForTableEventqueue()->setFileTable ( 'tx_extracache_eventqueue' );
		$this->getCacheDatabaseEntryRepositoryForTableEventqueue()->setFieldForCountOperation( 'id' );
		$this->getCacheDatabaseEntryRepositoryForTableEventqueue()->setOrderBy( 'first_called_time,status' );
		$this->getCacheDatabaseEntryRepositoryForTablePages()->setFileTable ( 'pages' );
		$this->getCacheDatabaseEntryRepositoryForTablePages()->setFieldForCountOperation( 'uid' );
		$this->getCacheDatabaseEntryRepositoryForTablePages()->setOrderBy ( 'pid ASC, title ASC' );
		$this->getCacheDatabaseEntryRepositoryForTableStaticCache()->setFileTable ( $this->getExtensionManager()->get('fileTable') );
		$this->getCacheDatabaseEntryRepositoryForTableStaticCache()->setFieldForCountOperation( 'uid' );
		$this->getCacheDatabaseEntryRepositoryForTableStaticCache()->setOrderBy( 'host,uri' );
		$this->getCacheFileRepository()->setCacheDir ( PATH_site . $this->getExtensionManager()->get('path_StaticFileCache') );

		// load translations
		$GLOBALS['LANG']->includeLLFile('EXT:extracache/Resources/Private/Language/locallang.xml');
	}

	/**
	 * show all database-entries of table 'tx_extracache_eventlog'
	 * 
	 * @return string
	 */
	public function allDatabaseEntrysForTableEventlogAction() {
		try {
			$startDateFilterForDbRecords = strtotime($this->getModuleData('tx_extracache_manager_startDateFilterForDbRecords'));
			$stopDateFilterForDbRecords = strtotime($this->getModuleData('tx_extracache_manager_stopDateFilterForDbRecords'));
			if(is_integer($startDateFilterForDbRecords) === FALSE) {
				$startDateFilterForDbRecords = 0;
			}
			if(is_integer($stopDateFilterForDbRecords) === FALSE) {
				$stopDateFilterForDbRecords = 0;
			} else {
				$stopDateFilterForDbRecords += 86399;
			}
			$sqlWhere = '1=1';
			if($startDateFilterForDbRecords > 0) {
				$sqlWhere .= ' AND start_time >='.$startDateFilterForDbRecords;
			}
			if($stopDateFilterForDbRecords > 0) {
				$sqlWhere .= ' AND stop_time <='.$stopDateFilterForDbRecords;
			}
			$this->getView()->assign ( 'allDatabaseEntrysForTableEventlog', $this->getCacheDatabaseEntryRepositoryForTableEventlog()->query ($sqlWhere) );
			return $this->getView()->render ( 'allDatabaseEntrysForTableEventlog' );
		} catch (Exception $e) {
			return $this->showErrorMessage($e);
		}
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
			$searchPhrase = (string) $this->getModuleData('tx_extracache_manager_searchPhraseForTablePages');
			$sqlWhere = $this->createSqlWhereClauseForDbRecords($searchPhrase, array('tstamp','crdate','starttime','endtime'));
			$this->getView()->assign ( 'allDatabaseEntrysForTablePages', $this->getCacheDatabaseEntryRepositoryForTablePages()->query ( '(tx_extracache_cleanerstrategies!=\'\' OR tx_extracache_events!=\'\') AND '.$sqlWhere ) );
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
			$searchPhrase = (string) $this->getModuleData('tx_extracache_manager_searchPhraseForTableStaticCache');
			$sqlWhere = $this->createSqlWhereClauseForDbRecords($searchPhrase, array('tstamp','crdate'));
			$this->getView()->assign ( 'allDatabaseEntrysForTableStaticCache', $this->getCacheDatabaseEntryRepositoryForTableStaticCache()->query ($sqlWhere) );
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
			$searchPhraseForFiles = (string) $this->getModuleData('tx_extracache_manager_searchPhraseForFiles');
			$this->getView()->assign ( 'allFiles', $this->getCacheFileRepository()->getAll ($searchPhraseForFiles) );
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
			$searchPhraseForFolders = (string) $this->getModuleData('tx_extracache_manager_searchPhraseForFolders');
			$getFoldersWhichDoesNotContainFiles = $this->getModuleData('tx_extracache_manager_getFoldersWhichDoesNotContainFiles') === TRUE ? TRUE : FALSE;
			$this->getView()->assign ( 'allFolders', $this->getCacheFileRepository()->getAllFolders ( $getFoldersWhichDoesNotContainFiles, $searchPhraseForFolders ) );
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
			$showDatabaseDetails = $this->getModuleData('tx_extracache_manager_showDatabaseDetails') === TRUE ? TRUE : FALSE;
			$showFilesDetails = $this->getModuleData('tx_extracache_manager_showFilesDetails') === TRUE ? TRUE : FALSE;
	
			$this->getView()->assign ( 'showDatabaseDetails', $showDatabaseDetails);
			$this->getView()->assign ( 'showFilesDetails', $showFilesDetails);
			$this->getView()->assign ( 'tableEventLog', $this->getCacheDatabaseEntryRepositoryForTableEventlog()->getFileTable () );
			$this->getView()->assign ( 'tableEventQueue', $this->getCacheDatabaseEntryRepositoryForTableEventqueue()->getFileTable () );
			$this->getView()->assign ( 'tablePages', $this->getCacheDatabaseEntryRepositoryForTablePages()->getFileTable () );
			$this->getView()->assign ( 'tableStaticCache', $this->getCacheDatabaseEntryRepositoryForTableStaticCache()->getFileTable () );

			if($showDatabaseDetails) {
				$this->getView()->assign ( 'countDatbaseEntrysForTableEventlog', $this->getCacheDatabaseEntryRepositoryForTableEventlog()->countAll () );
				$this->getView()->assign ( 'countDatbaseEntrysForTableEventqueue', $this->getCacheDatabaseEntryRepositoryForTableEventqueue()->countAll () );
				$this->getView()->assign ( 'countDatbaseEntrysForTablePages', $this->getCacheDatabaseEntryRepositoryForTablePages()->count ( 'tx_extracache_cleanerstrategies!=\'\' OR tx_extracache_events!=\'\'' ) );
				$this->getView()->assign ( 'countDatbaseEntrysForTableStaticCache', $this->getCacheDatabaseEntryRepositoryForTableStaticCache()->countAll () );
			}
			if($showFilesDetails) {
				$this->getView()->assign ( 'countFiles', $this->getCacheFileRepository()->countAll () );
			}

			return $this->getView()->render ( 'index' );
		} catch (Exception $e) {
			return $this->showErrorMessage($e);
		}	
	}
	/**
	 * @return string
	 */
	public function setConfigDateFilterForDbRecordsAction() {
		try {
			$GLOBALS['BE_USER']->pushModuleData('tx_extracache_manager_startDateFilterForDbRecords', t3lib_div::_GP('startDateFilterForDbRecords'));
			$GLOBALS['BE_USER']->pushModuleData('tx_extracache_manager_stopDateFilterForDbRecords', t3lib_div::_GP('stopDateFilterForDbRecords'));
			switch(t3lib_div::_GP('routeToAction')) {
				case 'allDatabaseEntrysForTableEventlogAction':
					return $this->allDatabaseEntrysForTableEventlogAction();
					break;
				default:
					return $this->allFoldersAction();
					break;
			}
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
	 * @return string
	 */
	public function setConfigSearchPhraseForTablePagesAction() {
		try {
			$GLOBALS['BE_USER']->pushModuleData('tx_extracache_manager_searchPhraseForTablePages', (string) t3lib_div::_GP('searchPhraseForTablePages'));
			return $this->allDatabaseEntrysForTablePagesAction();
		} catch (Exception $e) {
			return $this->showErrorMessage($e);
		}
	}
	/**
	 * @return string
	 */
	public function setConfigSearchPhraseForTableStaticCacheAction() {
		try {
			$GLOBALS['BE_USER']->pushModuleData('tx_extracache_manager_searchPhraseForTableStaticCache', (string) t3lib_div::_GP('searchPhraseForTableStaticCache'));
			return $this->allDatabaseEntrysForTableStaticCacheAction();
		} catch (Exception $e) {
			return $this->showErrorMessage($e);
		}
	}
	/**
	 * @return string
	 */
	public function setConfigSearchPhraseForFilesAction() {
		try {
			$GLOBALS['BE_USER']->pushModuleData('tx_extracache_manager_searchPhraseForFiles', (string) t3lib_div::_GP('searchPhraseForFiles'));
			return $this->allFilesAction();
		} catch (Exception $e) {
			return $this->showErrorMessage($e);
		}
	}
	/**
	 * @return string
	 */
	public function setConfigSearchPhraseForFoldersAction() {
		try {
			$GLOBALS['BE_USER']->pushModuleData('tx_extracache_manager_searchPhraseForFolders', (string) t3lib_div::_GP('searchPhraseForFolders'));
			return $this->allFoldersAction();
		} catch (Exception $e) {
			return $this->showErrorMessage($e);
		}
	}
	/**
	 * @return string
	 */
	public function setConfigShowDatabaseDetailsAction() {
		try {
			$GLOBALS['BE_USER']->pushModuleData('tx_extracache_manager_showDatabaseDetails', (boolean) t3lib_div::_GP('showDatabaseDetails'));
			return $this->indexAction();
		} catch (Exception $e) {
			return $this->showErrorMessage($e);
		}
	}
	/**
	 * @return string
	 */
	public function setConfigShowFilesDetailsAction() {
		try {
			$GLOBALS['BE_USER']->pushModuleData('tx_extracache_manager_showFilesDetails', (boolean) t3lib_div::_GP('showFilesDetails'));
			return $this->indexAction();
		} catch (Exception $e) {
			return $this->showErrorMessage($e);
		}
	}

	/**
	 * @param	string $key
	 * @return	string
	 */
	protected function getModuleData($key) {
		return $GLOBALS['BE_USER']->getModuleData($key);
	}

	/**
	 * create SQL-where-clause for db-records
	 * 
	 * @param	string $searchPhrase
	 * @param	array $dbFieldsWhichContainTimeData
	 * @return	string
	 */
	private function createSqlWhereClauseForDbRecords($searchPhrase, array $dbFieldsWhichContainTimeData=array()) {
		$sqlWhere = '1=1';
		if($searchPhrase !== '') {
			list($field, $value) = explode(':', $searchPhrase);
			if(in_array($field, $dbFieldsWhichContainTimeData)) {
				$value = strtotime($value);
				if(is_integer($value) === TRUE) {
					$sqlWhere .= ' AND '.$field.' >='.$value;
				}
			} else {
				$sqlWhere .= ' AND '.$field.' like \'%'.$value.'%\'';
			}
		}
		return $sqlWhere;
	}
	/**
	 * @return Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository
	 */
	private function getCacheDatabaseEntryRepositoryForTableEventlog() {
		return $this->cacheDatabaseEntryRepositoryForTableEventlog;
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
	 * @param Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository $cacheDatabaseEntryRepositoryForTableEventlog
	 */
	private function setCacheDatabaseEntryRepositoryForTableEventlog(Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository $cacheDatabaseEntryRepositoryForTableEventlog) {
		$this->cacheDatabaseEntryRepositoryForTableEventlog = $cacheDatabaseEntryRepositoryForTableEventlog;
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