<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2010 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

#require_once dirname ( __FILE__ ) . '/../../AbstractTestcase.php';

/**
 * Test case for Tx_Extracache_Typo3_Hooks_StaticFileCache_CreateFileHook
 *
 * @package extracache_tests
 * @subpackage Typo3_Hooks_StaticFileCache
 */
class Tx_Extracache_Typo3_Hooks_StaticFileCache_CreateFileHookTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var Tx_Extracache_Domain_Repository_ArgumentRepository
	 */
	protected $argumentRepository;

	/**
	 * @var Tx_Extracache_Typo3_Hooks_StaticFileCache_CreateFileHook
	 */
	protected $createFileHook;

	/**
	 * @var tx_ncstaticfilecache
	 */
	protected $staticFileCache;

	/**
	 * @var tslib_feUserAuth
	 */
	protected $frontendUser;

	/**
	 * @var tslib_fe
	 */
	protected $frontend;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();

		$this->argumentRepository = new Tx_Extracache_Domain_Repository_ArgumentRepository();
		$this->argumentRepository->addArgument(new Tx_Extracache_Domain_Model_Argument('clean', Tx_Extracache_Domain_Model_Argument::TYPE_ignoreOnCreatingCache, TRUE));
		$this->argumentRepository->addArgument(new Tx_Extracache_Domain_Model_Argument('whitelist', Tx_Extracache_Domain_Model_Argument::TYPE_whitelist, TRUE));

		$this->staticFileCache = $this->getMock('tx_ncstaticfilecache');

		$this->frontendUser = $this->getMock('tslib_feUserAuth', array());
		$this->frontend = $this->getMock ('tslib_fe', array(), array(), '', FALSE);
		$this->frontend->fe_user = $this->frontendUser;

		$this->createFileHook = $this->getMock('Tx_Extracache_Typo3_Hooks_StaticFileCache_CreateFileHook', array('getArgumentRepository', 'getFrontendUserGroupList'));
		$this->createFileHook->expects($this->any())->method('getArgumentRepository')->will($this->returnValue($this->argumentRepository));
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		parent::tearDown();

		unset($this->argumentRepository);
		unset($this->staticFileCache);
		unset($this->frontendUser);
		unset($this->frontend);
		unset($this->createFileHook);
	}

	/**
	 * Tests whether all arguments could be cleaned as defined.
	 *
	 * @return	void
	 * @test
	 */
	public function canCleanAllArgumentsSimple() {
		$uri = 'path-segment/?clean[first]=value&clean[second]=value';

		$parameters = array ('uri' => &$uri, 'TSFE' => $this->frontend);
		$this->createFileHook->initialize($parameters, $this->staticFileCache);

		$this->assertEquals('path-segment/', $uri);
	}

	/**
	 * Tests whether all arguments could not be cleaned since a FOREIGN one appears.
	 *
	 * @return	void
	 * @test
	 */
	public function cannotCleanAllArguments() {
		$uri = 'path-segment/?clean[first]=value&unknown=1&clean[second]=value';

		$parameters = array('uri' => &$uri, 'TSFE' => $this->frontend);
		$this->createFileHook->initialize($parameters, $this->staticFileCache);

		$this->assertEquals('path-segment/?clean[first]=value&unknown=1&clean[second]=value', $uri );
	}

	/**
	 * Tests whether the cache directory contains the frontend user group list.
	 *
	 * @return void
	 * @test
	 */
	public function doesCacheDirectoryContainFrontendUserGroupList() {
		$frontendUserGroupList = '0,-2,3,4,5';
		$this->createFileHook->expects($this->once())->method('getFrontendUserGroupList')->will($this->returnValue($frontendUserGroupList));

		$initialCacheDirectory = $cacheDirectory = uniqid('directory');

		$parameters = array('cacheDir' => &$cacheDirectory, 'TSFE' => $this->frontend);
		$this->createFileHook->initialize($parameters, $this->staticFileCache);

		$this->assertEquals($initialCacheDirectory . '/' . $frontendUserGroupList, $cacheDirectory);
	}

	/**
	 * Tests whether the cache directory contains the frontend user group list.
	 *
	 * @return void
	 * @test
	 */
	public function doesAdditionalHashContainFrontendUserGroupList() {
		$frontendUserGroupList = '0,-2,3,4,5';
		$this->createFileHook->expects($this->once())->method('getFrontendUserGroupList')->will($this->returnValue($frontendUserGroupList));

		$additionalHash = '';

		$parameters = array('additionalHash' => &$additionalHash, 'TSFE' => $this->frontend);
		$this->createFileHook->initialize($parameters, $this->staticFileCache);

		$this->assertEquals(md5($frontendUserGroupList), $additionalHash);
	}

	/**
	 * Tests whether the cache directory contains the frontend user group list.
	 *
	 * @return void
	 * @test
	 */
	public function doFieldValuesContainFrontendUserGroupList() {
		$frontendUserGroupList = '0,-2,3,4,5';
		$this->createFileHook->expects($this->once())->method('getFrontendUserGroupList')->will($this->returnValue($frontendUserGroupList));

		$fieldValues = array();

		$parameters = array('fieldValues' => &$fieldValues, 'TSFE' => $this->frontend);
		$this->createFileHook->initialize($parameters, $this->staticFileCache);

		$this->assertEquals($fieldValues[Tx_Extracache_Typo3_Hooks_StaticFileCache_CreateFileHook::FIELD_GroupList], $frontendUserGroupList);
	}
}