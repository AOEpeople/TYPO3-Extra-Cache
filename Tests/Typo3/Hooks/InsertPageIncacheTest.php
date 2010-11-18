<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2010 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once dirname ( __FILE__ ) . '/../../AbstractTestcase.php';

/**
 * Test case for Tx_Extracache_Typo3_Hooks_InsertPageIncache
 *
 * @package extracache_tests
 * @subpackage Typo3_Hooks
 */
class Tx_Extracache_Typo3_Hooks_InsertPageIncacheTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var Tx_Extracache_Typo3_Hooks_InsertPageIncache
	 */
	private $hook;
	/**
	 * @var tslib_fe
	 */
	private $tsfe;
	/**
	 * @var Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	private $typo3DbBackend;
	
	/**
	 * Sets up this test case.
	 */
	protected function setUp() {
		$this->loadClass('Tx_Extracache_System_Persistence_Typo3DbBackend');
		$this->loadClass('Tx_Extracache_Typo3_Hooks_InsertPageIncache');

		$this->tsfe = $this->getMock('tslib_fe', array(), array(), '', FALSE);
		$this->typo3DbBackend = $this->getMock('Tx_Extracache_System_Persistence_Typo3DbBackend', array(), array(), '', FALSE);
		$this->hook = $this->getMock('Tx_Extracache_Typo3_Hooks_InsertPageIncache', array('getTypo3DbBackend'));
		$this->hook->expects($this->any())->method('getTypo3DbBackend')->will($this->returnValue( $this->typo3DbBackend ));
	}
	/**
	 * Cleans this test case
	 */
	protected function tearDown() {
		unset($this->hook);
		unset($this->tsfe);
		unset($this->typo3DbBackend);
	}

	/**
	 * Test method insertPageIncache
	 * @test
	 */
	public function insertPageIncache_dontUpdateCacheTable() {
		$this->tsfe->id = 5;
		$this->tsfe->gr_list = '0,-1';
		$this->tsfe->newHash = 'newHash';
		$this->typo3DbBackend->expects($this->never())->method('fullQuoteStr');
		$this->typo3DbBackend->expects($this->never())->method('updateQuery');
		$this->hook->insertPageIncache($this->tsfe, 0);
	}
	/**
	 * Test method insertPageIncache
	 * @test
	 */
	public function insertPageIncache_updateCacheTable() {
		$this->tsfe->id = 5;
		$this->tsfe->gr_list = '1,2,3,4';
		$this->tsfe->newHash = 'newHash';
		$this->typo3DbBackend->expects($this->once())->method('fullQuoteStr')->will($this->returnValue( '"cache_pages.'.$this->tsfe->newHash.'"' ));
		$this->typo3DbBackend->expects($this->once())->method('updateQuery')->with('cache_pages', 'hash="cache_pages.'.$this->tsfe->newHash.'" AND page_id='.$this->tsfe->id, array('tx_extracache_grouplist' => $this->tsfe->gr_list));
		$this->hook->insertPageIncache($this->tsfe, 0);
	}
}