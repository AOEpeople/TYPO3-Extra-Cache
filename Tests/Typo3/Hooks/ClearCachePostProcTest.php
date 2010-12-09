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
 * Test case for tx_Extracache_Typo3_Hooks_ClearCachePostProc
 *
 * @package extracache_tests
 * @subpackage Typo3_Hooks
 */
class Tx_Extracache_Typo3_Hooks_ClearCachePostProcTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var	tx_Extracache_Typo3_Hooks_ClearCachePostProc
	 */
	private $hook;
	/**
	 * @var Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	private $typo3DbBackend;

	/**
	 * Set up
	 */
	protected function setUp() {
		$this->loadClass('tx_Extracache_Typo3_Hooks_ClearCachePostProc');
		$this->typo3DbBackend = $this->getMock('Tx_Extracache_System_Persistence_Typo3DbBackend', array(), array(), '', FALSE);
		$this->hook = $this->getMock('tx_Extracache_Typo3_Hooks_ClearCachePostProc', array('getTypo3DbBackend'));
		$this->hook->expects($this->any())->method('getTypo3DbBackend')->will($this->returnValue($this->typo3DbBackend));
	}
	/**
	 * clean up
	 */
	protected function tearDown() {
		unset($this->hook);
		unset($this->typo3DbBackend);
	}

	/**
	 * Test Method clearCachePostProc
	 * @test
	 */
	public function clearCachePostProc() {
		$this->typo3DbBackend->expects($this->once())->method('deleteQuery')->with('tx_extracache_eventqueue', '');
		$this->hook->clearCachePostProc( array('cacheCmd' => 'all') );

		$this->setUp();
		$this->typo3DbBackend->expects($this->once())->method('deleteQuery')->with('tx_extracache_eventqueue', '');
		$this->hook->clearCachePostProc( array('cacheCmd' => 'pages') );

		$this->setUp();
		$this->typo3DbBackend->expects($this->never())->method('deleteQuery');
		$this->hook->clearCachePostProc( array('cacheCmd' => 'test') );
	}
}