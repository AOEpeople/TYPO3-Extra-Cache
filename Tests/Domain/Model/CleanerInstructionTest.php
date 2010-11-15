<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2010 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once dirname ( __FILE__ ) . '/../../AbstractDatabaseTestcase.php';

/**
 * test case for Tx_Extracache_Domain_Model_CleanerInstruction
 * @package extracache_tests
 * @subpackage Domain_Model
 */
class Tx_Extracache_Domain_Model_CleanerInstructionTest extends Tx_Extracache_Tests_AbstractDatabaseTestcase {
	/**
	 * @var Tx_Extracache_Domain_Model_CleanerInstruction
	 */
	private $cleanerInstruction;
	/**
	 * @var Tx_Extracache_Domain_Model_CleanerStrategy
	 */
	private $mockedCleanerStrategy;
	/**
	 * @var tx_ncstaticfilecache
	 */
	private $mockedStaticFileCache;
	/**
	 * @var Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	private $mockedTypo3DbBackend;
	/**
	 * @var t3lib_TCEmain
	 */
	private $mockedTceMain;
	/**
	 * @var array
	 */
	private $pageIds;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->loadClass('Tx_Extracache_Domain_Model_CleanerStrategy');
		$this->loadClass('Tx_Extracache_System_Persistence_Typo3DbBackend');
		$this->mockedCleanerStrategy = $this->getMock ( 'Tx_Extracache_Domain_Model_CleanerStrategy', array(), array(), '', FALSE);
		$this->mockedStaticFileCache = $this->getMock ( 'tx_ncstaticfilecache', array('deleteStaticCacheDirectory', 'processDirtyPagesElement'));
		$this->mockedTypo3DbBackend  = $this->getMock ( 'Tx_Extracache_System_Persistence_Typo3DbBackend', array('deleteQuery','updateQuery'));
		$this->mockedTceMain = $this->getMock ( 't3lib_TCEmain', array(), array(), '', FALSE);
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		unset ( $this->mockedCleanerStrategy );
		unset ( $this->mockedStaticFileCache );
		unset ( $this->mockedTypo3DbBackend );
		unset ( $this->mockedTceMain );
		unset ( $this->cleanerInstruction );
	}

	/**
	 * Tests whether empty configuration is parsed and but not processed.
	 * @test
	 */
	public function isEmptyConfigurationParsedAndNotProcessed() {
		$this->createTestDB();

		$pageId = $this->pageIds[0];
		$this->mockedTceMain->expects($this->never())->method('clear_cacheCmd');
		$this->mockedStaticFileCache->expects($this->never())->method('deleteStaticCacheDirectory');
		$this->mockedStaticFileCache->expects($this->never())->method('processDirtyPagesElement');
		$this->mockedTypo3DbBackend->expects($this->never())->method('updateQuery');
		$this->mockedTypo3DbBackend->expects($this->never())->method('deleteQuery');

		$actions = Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_None;
		$childrenMode = NULL;
		$elementsMode = NULL;
		$this->processCleanerInstruction($pageId, $actions, $childrenMode, $elementsMode);

		$this->dropDatabase();
	}
	/**
	 * Tests whether clearing TYPO3 cache is processed.
	 * @test
	 */
	public function isTYPO3ClearProcessedCorrectly() {
		$this->createTestDB();

		$this->mockedTceMain->expects($this->once())->method('clear_cacheCmd')->with($this->pageIds[0]);
		$this->mockedStaticFileCache->expects($this->never())->method('deleteStaticCacheDirectory');
		$this->mockedStaticFileCache->expects($this->never())->method('processDirtyPagesElement');
		$this->mockedTypo3DbBackend->expects($this->never())->method('updateQuery');
		$this->mockedTypo3DbBackend->expects($this->never())->method('deleteQuery');

		$actions = Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_TYPO3Clear;
		$childrenMode = Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenNoAction;
		$elementsMode = Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsNoAction;
		$this->processCleanerInstruction($this->pageIds[0], $actions, $childrenMode, $elementsMode);

		$this->dropDatabase();
	}
	/**
	 * Tests whether clearing TYPO3 cache is processed on children only.
	 * @test
	 */
	public function isTYPO3ClearProcessedCorrectlyOnChildrenOnly() {
		$this->createTestDB();

		$this->mockedTceMain->expects($this->exactly(2))->method('clear_cacheCmd');
		$this->mockedTceMain->expects($this->at ( 0 ))->method('clear_cacheCmd')->with($this->pageIds[1]);
		$this->mockedTceMain->expects($this->at ( 1 ))->method('clear_cacheCmd')->with($this->pageIds[2]);
		$this->mockedStaticFileCache->expects($this->never())->method('deleteStaticCacheDirectory');
		$this->mockedStaticFileCache->expects($this->never())->method('processDirtyPagesElement');
		$this->mockedTypo3DbBackend->expects($this->never())->method('updateQuery');
		$this->mockedTypo3DbBackend->expects($this->never())->method('deleteQuery');

		$actions = Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_TYPO3Clear;
		$childrenMode = Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenOnly;
		$elementsMode = Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsNoAction;
		$this->processCleanerInstruction($this->pageIds[0], $actions, $childrenMode, $elementsMode);

		$this->dropDatabase();
	}
	/**
	 * Tests whether clearing TYPO3 cache is processed on all children.
	 * @test
	 */
	public function isTYPO3ClearProcessedCorrectlyOnAllChildren() {
		$this->createTestDB();

		$this->mockedTceMain->expects($this->exactly(3))->method('clear_cacheCmd');
		$this->mockedTceMain->expects($this->at ( 0 ))->method('clear_cacheCmd')->with($this->pageIds[0]);
		$this->mockedTceMain->expects($this->at ( 1 ))->method('clear_cacheCmd')->with($this->pageIds[1]);
		$this->mockedTceMain->expects($this->at ( 2 ))->method('clear_cacheCmd')->with($this->pageIds[2]);
		$this->mockedStaticFileCache->expects($this->never())->method('deleteStaticCacheDirectory');
		$this->mockedStaticFileCache->expects($this->never())->method('processDirtyPagesElement');
		$this->mockedTypo3DbBackend->expects($this->never())->method('updateQuery');
		$this->mockedTypo3DbBackend->expects($this->never())->method('deleteQuery');

		$actions = Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_TYPO3Clear;
		$childrenMode = Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenWithParent;
		$elementsMode = Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsNoAction;
		$this->processCleanerInstruction($this->pageIds[0], $actions, $childrenMode, $elementsMode);

		$this->dropDatabase();
	}
	/**
	 * Tests whether updating static cache is processed.
	 * @test
	 */
	public function isStaticUpdateProcessedCorrectly() {
		$this->createTestDB();

		$this->mockedTceMain->expects($this->never())->method('clear_cacheCmd');
		$this->mockedStaticFileCache->expects($this->never())->method('deleteStaticCacheDirectory');
		$this->mockedStaticFileCache->expects($this->once())->method('processDirtyPagesElement');
		$this->mockedTypo3DbBackend->expects($this->never())->method('updateQuery');
		$this->mockedTypo3DbBackend->expects($this->never())->method('deleteQuery');

		$actions = Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_StaticUpdate;
		$childrenMode = Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenNoAction;
		$elementsMode = Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsNoAction;
		$this->processCleanerInstruction($this->pageIds[0], $actions, $childrenMode, $elementsMode);
		
		$this->dropDatabase();
	}
	
	/**
	 * Tests whether clearing static cache is processed.
	 * @test
	 */
	public function isStaticClearProcessedCorrectly() {
		$this->createTestDB();
		
		$this->mockedTceMain->expects($this->never())->method('clear_cacheCmd');
		$this->mockedStaticFileCache->expects($this->never())->method('processDirtyPagesElement');
		$this->mockedStaticFileCache->expects($this->once())->method('deleteStaticCacheDirectory')->with('nico/0,1/page')->will ( $this->returnValue ( TRUE ) );
		$this->mockedTypo3DbBackend->expects($this->once())->method('deleteQuery')->with('tx_ncstaticfilecache_file','uid=1');
		$this->mockedTypo3DbBackend->expects($this->never())->method('updateQuery');

		$actions = Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_StaticClear;
		$childrenMode = Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenNoAction;
		$elementsMode = Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsNoAction;
		$this->processCleanerInstruction($this->pageIds[0], $actions, $childrenMode, $elementsMode);
		
		$this->dropDatabase();	
	}
	/**
	 * Tests whether clearing static cache is processed on all children and elements.
	 * @test
	 */
	public function isStaticClearProcessedCorrectlyOnAllChildrenAndElements() {
		$this->createTestDB();

		$this->mockedTceMain->expects($this->never())->method('clear_cacheCmd');
		$this->mockedStaticFileCache->expects($this->never())->method('processDirtyPagesElement');
		$this->mockedStaticFileCache->expects($this->exactly(6))->method('deleteStaticCacheDirectory');
		$this->mockedStaticFileCache->expects($this->at ( 0 ))->method('deleteStaticCacheDirectory')->with('nico/0,1/page')->will ( $this->returnValue ( TRUE ) );
		$this->mockedStaticFileCache->expects($this->at ( 1 ))->method('deleteStaticCacheDirectory')->with('nico/0,1/page/argument/1')->will ( $this->returnValue ( TRUE ) );
		$this->mockedStaticFileCache->expects($this->at ( 2 ))->method('deleteStaticCacheDirectory')->with('nico/0,1/page/sub1')->will ( $this->returnValue ( TRUE ) );
		$this->mockedStaticFileCache->expects($this->at ( 3 ))->method('deleteStaticCacheDirectory')->with('nico/0,1/page/sub1/argument/1')->will ( $this->returnValue ( TRUE ) );
		$this->mockedStaticFileCache->expects($this->at ( 4 ))->method('deleteStaticCacheDirectory')->with('nico/0,1/page/sub2')->will ( $this->returnValue ( TRUE ) );
		$this->mockedStaticFileCache->expects($this->at ( 5 ))->method('deleteStaticCacheDirectory')->with('nico/0,1/page/sub2/argument/1')->will ( $this->returnValue ( TRUE ) );
		$this->mockedTypo3DbBackend->expects($this->exactly(6))->method('deleteQuery');
		$this->mockedTypo3DbBackend->expects($this->at ( 0 ))->method('deleteQuery')->with('tx_ncstaticfilecache_file','uid=1');
		$this->mockedTypo3DbBackend->expects($this->at ( 1 ))->method('deleteQuery')->with('tx_ncstaticfilecache_file','uid=2');
		$this->mockedTypo3DbBackend->expects($this->at ( 2 ))->method('deleteQuery')->with('tx_ncstaticfilecache_file','uid=3');
		$this->mockedTypo3DbBackend->expects($this->at ( 3 ))->method('deleteQuery')->with('tx_ncstaticfilecache_file','uid=4');
		$this->mockedTypo3DbBackend->expects($this->at ( 4 ))->method('deleteQuery')->with('tx_ncstaticfilecache_file','uid=5');
		$this->mockedTypo3DbBackend->expects($this->at ( 5 ))->method('deleteQuery')->with('tx_ncstaticfilecache_file','uid=6');
		$this->mockedTypo3DbBackend->expects($this->never())->method('updateQuery');
		
		$actions = Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_StaticClear;
		$childrenMode = Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenWithParent;
		$elementsMode = Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsWithParent;
		$this->processCleanerInstruction($this->pageIds[0], $actions, $childrenMode, $elementsMode);

		$this->dropDatabase();	
	}
	/**
	 * Tests whether marking static cache dirty is processed.
	 * @test
	 */
	public function isStaticDirtyProcessedCorrectly() {
		$this->createTestDB();

		$this->mockedTceMain->expects($this->never())->method('clear_cacheCmd');
		$this->mockedStaticFileCache->expects($this->never())->method('processDirtyPagesElement');
		$this->mockedStaticFileCache->expects($this->never())->method('deleteStaticCacheDirectory');
		$this->mockedTypo3DbBackend->expects($this->once())->method('updateQuery')->with('tx_ncstaticfilecache_file','uid=1',array('isdirty'=>1));
		$this->mockedTypo3DbBackend->expects($this->never())->method('deleteQuery');
		
		$actions = Tx_Extracache_Domain_Model_CleanerStrategy::ACTION_StaticDirty;
		$childrenMode = Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ChildrenNoAction;
		$elementsMode = Tx_Extracache_Domain_Model_CleanerStrategy::CONSIDER_ElementsNoAction;
		$this->processCleanerInstruction($this->pageIds[0], $actions, $childrenMode, $elementsMode);

		$this->dropDatabase();
	}

	/**
	 * @param integer	$pageId
	 * @param integer	$actions
	 * @param string	$childrenMode
	 * @param string	$elementsMode
	 */
	private function processCleanerInstruction($pageId, $actions, $childrenMode, $elementsMode) {
		// configure cleanerStrategy
		$this->mockedCleanerStrategy->expects($this->any())->method('getActions')->will ( $this->returnValue ( $actions ) );
		$this->mockedCleanerStrategy->expects($this->any())->method('getChildrenMode')->will ( $this->returnValue ( $childrenMode ) );
		$this->mockedCleanerStrategy->expects($this->any())->method('getElementsMode')->will ( $this->returnValue ( $elementsMode ) );

		// create and process cleanerInstruction
		$this->cleanerInstruction = t3lib_div::makeInstance('Tx_Extracache_Domain_Model_CleanerInstruction', $this->mockedStaticFileCache, $this->mockedTceMain, $this->mockedTypo3DbBackend, $this->mockedCleanerStrategy, $pageId);
		$this->cleanerInstruction->process();
	}
	/**
	 * creates the test-database and insert records
	 */
	private function createTestDB() {
		$PATH_tx_extracache = t3lib_extMgm::extPath ( 'extracache' );
		
		global $TYPO3_DB;

		$this->createDatabase();
		// create tables 'pages' and 'cache_pages' manually (because it's faster than import the hole cms-extension)
		$db = $this->useTestDatabase();

		$db->admin_query( t3lib_div::getUrl( $PATH_tx_extracache . 'Tests/Domain/Model/Fixtures/SqlQueryForUnittestCleanerInstruction_createTablePages.txt' ) );
		$db->admin_query( t3lib_div::getUrl( $PATH_tx_extracache . 'Tests/Domain/Model/Fixtures/SqlQueryForUnittestCleanerInstruction_createTableCachePages.txt' ) );
		
		$this->importStdDB();
		$this->importExtensions(array('cms', 'realurl', 'nc_staticfilecache', 'extracache'));
		$this->initializeCommonExtensions();
		$this->importDataSet($PATH_tx_extracache . 'Tests/Domain/Model/Fixtures/TestRecordsForUnittestCleanerInstruction.xml');

		/********** get UID's of records of certain tables *****/
		$this->pageIds = array();
		$data = $TYPO3_DB->exec_SELECTgetRows ( 'uid', 'pages');
		foreach ($data as $row) {
			$this->pageIds[] = (int) $row['uid'];
		}

		/********** update records of pages and cache-tables (because we support aoe_dbsequenzer, we use dynamic page-UID's) *****/
		$TYPO3_DB->exec_UPDATEquery ( 'pages', 'pid=187', array('pid' => $this->pageIds[0]) );
		$TYPO3_DB->exec_UPDATEquery ( 'cache_pages', 'page_id=187', array('page_id' => $this->pageIds[0],'tags' => 'pageId_'.$this->pageIds[0]) );
		$TYPO3_DB->exec_UPDATEquery ( 'cache_pages', 'page_id=190', array('page_id' => $this->pageIds[1],'tags' => 'pageId_'.$this->pageIds[1]) );
		$TYPO3_DB->exec_UPDATEquery ( 'cache_pages', 'page_id=191', array('page_id' => $this->pageIds[2],'tags' => 'pageId_'.$this->pageIds[2]) );
		$TYPO3_DB->exec_UPDATEquery ( 'tx_ncstaticfilecache_file', 'pid=187', array('pid' => $this->pageIds[0]) );
		$TYPO3_DB->exec_UPDATEquery ( 'tx_ncstaticfilecache_file', 'pid=190', array('pid' => $this->pageIds[1]) );
		$TYPO3_DB->exec_UPDATEquery ( 'tx_ncstaticfilecache_file', 'pid=191', array('pid' => $this->pageIds[2]) );
	}
}