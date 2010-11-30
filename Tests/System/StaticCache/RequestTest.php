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

require_once dirname ( __FILE__ ) . '/../../AbstractTestcase.php';

/**
 * test case for Tx_Extracache_System_StaticCache_Request
 * @package extracache_tests
 * @subpackage System_StaticCache
 */
class Tx_Extracache_System_StaticCache_RequestTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var Tx_Extracache_System_StaticCache_Request
	 */
	private $request;
	/**
	 * @var array
	 */
	private $originalArgumentsGet;
	/**
	 * @var array
	 */
	private $originalArgumentsPost;
	/**
	 * @var array
	 */
	private $originalCookies;
	/**
	 * @var array
	 */
	private $originalServerVariables;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->originalArgumentsGet  = $_GET;
		$this->originalArgumentsPost = $_POST;
		$this->originalCookies = $_COOKIE;
		$this->originalServerVariables = $_SERVER;
		$_GET  = array( 'test_get1'  => '11' );
		$_POST = array( 'test_post1' => '12' );
		$_COOKIE = array( 'test_cookie1' => '13' );
		$_SERVER = array_merge( $_SERVER, array('test_server1' => '14') );
		$this->request = $this->getMock('Tx_Extracache_System_StaticCache_Request', array('getIndpEnvFromTypo3'));
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$_GET = $this->originalArgumentsGet;
		$_POST = $this->originalArgumentsPost;
		$_COOKIE = $this->originalCookies;
		$_SERVER = $this->originalServerVariables;
		unset ( $this->request );
	}

	/**
	 * Test Method 'getArgument'
	 * @test
	 */
	public function getArgument() {
		$this->assertTrue( $this->request->getArgument('test_get1') === '11' );
		$this->assertTrue( $this->request->getArgument('test_get2') === NULL );
		$this->assertTrue( $this->request->getArgument('test_post1') === '12' );
		$this->assertTrue( $this->request->getArgument('test_post2') === NULL );
	}
	/**
	 * Test Method 'getCookie'
	 * @test
	 */
	public function getCookie() {
		$this->assertTrue( $this->request->getCookie('test_cookie1') === '13' );
		$this->assertTrue( $this->request->getCookie('test_cookie2') === NULL );
	}
	/**
	 * Test Method 'getFileName'
	 * @test
	 */
	public function getFileName() {
		$this->request->expects($this->once())->method('getIndpEnvFromTypo3')->with('TYPO3_SITE_SCRIPT')->will($this->returnValue('index.php'));
		$this->assertTrue( $this->request->getFileName() === 'index.php' );
		$this->setUp();
		$this->request->expects($this->once())->method('getIndpEnvFromTypo3')->with('TYPO3_SITE_SCRIPT')->will($this->returnValue('index.php?test=1'));
		$this->assertTrue( $this->request->getFileName() === 'index.php' );
	}
	/**
	 * Test Method 'getFileNameWithQuery'
	 * @test
	 */
	public function getFileNameWithQuery() {
		$this->request->expects($this->once())->method('getIndpEnvFromTypo3')->with('TYPO3_SITE_SCRIPT')->will($this->returnValue('index.php?test=1'));
		$this->assertTrue( $this->request->getFileNameWithQuery() === 'index.php?test=1' );
	}
	/**
	 * Test Method 'getHostName'
	 * @test
	 */
	public function getHostName() {
		$this->request->expects($this->once())->method('getIndpEnvFromTypo3')->with('TYPO3_HOST_ONLY')->will($this->returnValue('www.typo3-host.com'));
		$this->assertTrue( $this->request->getHostName() === 'www.typo3-host.com' );
	}
	/**
	 * Test get-methods
	 * @test
	 */
	public function getServerVariable() {
		$this->assertTrue( $this->request->getServerVariable('test_server1') === '14' );
		$this->assertTrue( $this->request->getServerVariable('test_server2') === NULL );
	}
}