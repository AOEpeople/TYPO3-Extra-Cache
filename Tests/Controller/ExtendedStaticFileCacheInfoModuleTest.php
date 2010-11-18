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

require_once (PATH_tx_extracache . 'Classes/Controller/ExtendedStaticFileCacheInfoModule.php');
require_once dirname ( __FILE__ ) . '/../AbstractTestcase.php';

/**
 * Tx_Extracache_Controller_ExtendedStaticFileCacheInfoModule test case.
 * @package extracache_tests
 * @subpackage Controller
 */
class Tx_Extracache_Controller_ExtendedStaticFileCacheInfoModuleTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * test the compiling of the class
	 * @test
	 */
	public function testCompiling() {
		// only execute this test, if this extension defines the XCLASS (it could happen, that another extension defines the XCLASS)
		if(strstr($GLOBALS['TYPO3_CONF_VARS']['BE']['XCLASS']['ext/nc_staticfilecache/infomodule/class.tx_ncstaticfilecache_infomodule.php'], 'ExtendedStaticFileCacheInfoModule.php')) {
			$infomodule =  new ux_tx_ncstaticfilecache_infomodule();
		}
	}
}