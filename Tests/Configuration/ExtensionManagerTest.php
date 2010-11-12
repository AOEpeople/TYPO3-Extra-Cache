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
 * test case for Tx_Extracache_Configuration_ExtensionManager
 * @package extracache_tests
 * @subpackage Configuration
 */
class Tx_Extracache_Configuration_ExtensionManagerTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * 
	 * @var Tx_Extracache_Configuration_ExtensionManager
	 */
	private $extensionManager;
	/**
	 * original config of the extracache-Extension
	 */
	private $originalExtConfig;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->originalExtConfig = $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['extracache'];
		$modifiedExtConfig = unserialize($this->originalExtConfig);
		$modifiedExtConfig['path_StaticFileCache_level1'] = '/test_dir/dir2/';
		$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['extracache'] = serialize($modifiedExtConfig);

		$this->loadClass('Tx_Extracache_Configuration_ExtensionManager');
		$this->extensionManager = new Tx_Extracache_Configuration_ExtensionManager();
	}
	
	/**
	 * Test method get
	 * @test
	 */
	public function get() {
		$this->assertEquals($this->extensionManager->get('path_StaticFileCache_level1'), '/test_dir/dir2/');
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['extracache'] = $this->originalExtConfig;
		unset ( $this->extensionManager );
	}
}