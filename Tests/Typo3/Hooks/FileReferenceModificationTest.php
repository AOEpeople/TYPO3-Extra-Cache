<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2010 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once dirname ( __FILE__ ) . '/../../AbstractDatabaseTestcase.php';

/**
 * Test case for Tx_Extracache_Typo3_Hooks_FileReferenceModification
 *
 * @package extracache_tests
 * @subpackage Typo3_Hooks
 */
class Tx_Extracache_Typo3_Hooks_FileReferenceModificationTest extends Tx_Extracache_Tests_AbstractDatabaseTestcase {
	/**
	 * @var	tx_eft_typo3_hook_fileReferenceModificationHook
	 */
	private $hook;

	/**
	 * @var	t3lib_TCEmain
	 */
	private $tceMain;

	/**
	 * Set up
	 */
	protected function setUp() {
		$this->loadClass('Tx_Extracache_Typo3_Hooks_FileReferenceModification');

		$this->hook = $this->getMock('Tx_Extracache_Typo3_Hooks_FileReferenceModification', array('isStaticCacheEnabled'));
		$this->tceMain = $this->getMock('t3lib_TCEmain', array(), array(), '', FALSE);

		$this->createDatabase();
		$this->useTestDatabase();
		$this->importExtensions(array('extracache'));
		$this->initializeCommonExtensions();
	}
	/**
	 * clean up
	 */
	protected function tearDown() {
		$this->dropDatabase();

		unset($this->hook);
		unset($this->tceMain);
	}

	/**
	 * Tests whether files to be removed are processed if static caching is enabled.
	 *
	 * @test
	 * @return	void
	 */
	public function filesToBeRemovedAreProcessedIfStaticCacheIsEnabled() {
		$removeFileStore = array(PATH_site . 'testfile.jpg');
		$this->tceMain->removeFilesStore = $removeFileStore;

		$this->hook->expects($this->any())->method('isStaticCacheEnabled')->will($this->returnValue(TRUE));
		$this->hook->processDatamap_afterDatabaseOperations('update', 'test', '13', array(), $this->tceMain);

		$this->assertEquals(
			array(),
			$this->tceMain->removeFilesStore
		);
	}
	/**
	 * Tests whether files to be removed are not processed if static caching is disabled.
	 *
	 * @test
	 * @return	void
	 */
	public function filesToBeRemovedAreNotProcessedIfStaticCacheIsDisabled() {
		$removeFileStore = array(PATH_site . 'testfile.jpg');
		$this->tceMain->removeFilesStore = $removeFileStore;

		$this->hook->expects($this->any())->method('isStaticCacheEnabled')->will($this->returnValue(FALSE));
		$this->hook->processDatamap_afterDatabaseOperations('update', 'test', '13', array(), $this->tceMain);

		$this->assertEquals(
			$removeFileStore,
			$this->tceMain->removeFilesStore
		);
	}
	/**
	 * Tests whether files to be removed are written to the queue.
	 *
	 * @test
	 * @return	void
	 */
	public function filesToBeRemovedAreWrittenToTheQueue() {
		$removeFileStore = array(PATH_site . 'testfile.jpg');
		$this->tceMain->removeFilesStore = $removeFileStore;

		$this->hook->expects($this->any())->method('isStaticCacheEnabled')->will($this->returnValue(TRUE));
		$this->hook->processDatamap_afterDatabaseOperations('update', 'test', '13', array(), $this->tceMain);
		$this->hook->processDatamap_afterAllOperations($this->tceMain);

		list($row) = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', Tx_Extracache_Typo3_Hooks_FileReferenceModification::TABLE_Queue, '');

		$this->assertEquals(
			'testfile.jpg',
			$row['files']
		);
	}
}