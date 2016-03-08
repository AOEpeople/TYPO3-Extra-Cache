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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * test case for Tx_Extracache_System_Tools_Uri
 * @package extracache_tests
 * @subpackage System_Tools
 */
class Tx_Extracache_System_Tools_UriTest extends Tx_Extracache_Tests_AbstractTestcase {
	/**
	 * @var	array
	 */
	private $ignoreArgumentsConfiguration;
	/**
	 * (non-PHPdoc)
	 * @see PHPUnit/Framework/PHPUnit_Framework_TestCase#setUp()
	 */
	protected function setUp() {
		$this->ignoreArgumentsConfiguration = array();
		$this->ignoreArgumentsConfiguration[] = GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_Argument', 'eftbasket', Tx_Extracache_Domain_Model_Argument::TYPE_ignoreOnCreatingCache, TRUE);
		$this->ignoreArgumentsConfiguration[] = GeneralUtility::makeInstance('Tx_Extracache_Domain_Model_Argument', 'cHash', Tx_Extracache_Domain_Model_Argument::TYPE_ignoreOnCreatingCache, TRUE);
	}
	/**
	 * (non-PHPdoc)
	 * @see PHPUnit/Framework/PHPUnit_Framework_TestCase#tearDown()
	 */
	protected function tearDown() {
		unset($this->ignoreArgumentsConfiguration);
	}

	/**
	 * Tests whether an URI can be filtered completely.
	 * @test
	 */
	public function isUriFilteredCompletelyRegular() {
		$uri = Tx_Extracache_System_Tools_Uri::filterUriArguments(
			'/path/?eftbasket[action]=add&eftbasket[key]=value&cHash=abcdef',
			$this->ignoreArgumentsConfiguration
		);
		$this->assertEquals('/path/', $uri);
	}

	/**
	 * Tests whether an URI can be filtered completely.
	 * @test
	 */
	public function isUriFilteredCompletelyEncoded() {
		$uri = Tx_Extracache_System_Tools_Uri::filterUriArguments(
			'/path/?' . urlencode('eftbasket[action]=add&eftbasket[key]=value&cHash=abcdef'),
			$this->ignoreArgumentsConfiguration
		);
		$this->assertEquals('/path/', $uri);
	}

	/**
	 * Tests whether at least one argument remains and URI stays unmodified.
	 * @test
	 */
	public function isUriFilteredButNotModified() {
		$originalUri = '/path/?eftbasket[action]=add&eftbasket[key]=value&cHash=abcdef&changingBehaviour=13';
		$uri = Tx_Extracache_System_Tools_Uri::filterUriArguments(
			$originalUri,
			$this->ignoreArgumentsConfiguration
		);
		$this->assertEquals($originalUri, $uri);
	}

	/**
	 * Tests whether an index URI can be determined.
	 * @test
	 */
	public function canDetermineIndexUri() {
		$this->assertTrue(Tx_Extracache_System_Tools_Uri::isIndexUri('/index.php'));
		$this->assertTrue(Tx_Extracache_System_Tools_Uri::isIndexUri('index.php'));
		$this->assertTrue(Tx_Extracache_System_Tools_Uri::isIndexUri('index.php.something'));

		$this->assertFalse(Tx_Extracache_System_Tools_Uri::isIndexUri('/index.html'));
		$this->assertFalse(Tx_Extracache_System_Tools_Uri::isIndexUri('index.html'));
		$this->assertFalse(Tx_Extracache_System_Tools_Uri::isIndexUri('otherindex.phpl'));
	}

	/**
	 * Tests whether an index URI is fixed.
	 * @test
	 */
	public function isUriIndexFixed() {
		$originalUri = '/' . Tx_Extracache_System_Tools_Uri::TYPO3_Index;
		$uri = Tx_Extracache_System_Tools_Uri::fixIndexUri($originalUri);
		$this->assertEquals('/', $uri);
	}

	/**
	 * Tests whether an index URI is fixed when it's faulty.
	 * @test
	 */
	public function isUriIndexFixedWhenFaulty() {
		$originalUri = '/' . Tx_Extracache_System_Tools_Uri::TYPO3_Index . '.something';
		$uri = Tx_Extracache_System_Tools_Uri::fixIndexUri($originalUri);
		$this->assertEquals('/', $uri);
	}

	/**
	 * Tests whether an index URI is not fixed if there is a path segment.
	 * @return unknown_type
	 */
	public function isUriIndexNotFixedOnExistingPath() {
		$originalUri = '/path/' . Tx_Extracache_System_Tools_Uri::TYPO3_Index;
		$uri = Tx_Extracache_System_Tools_Uri::fixIndexUri($originalUri);
		$this->assertEquals($originalUri, $uri);
	}

	/**
	 * Tests whether an index URI is not fixed if there are query arguments.
	 * @test
	 */
	public function isUriIndexNotFixedOnExistingQueryArguments() {
		$originalUri = '/' . Tx_Extracache_System_Tools_Uri::TYPO3_Index . '?id=13';
		$uri = Tx_Extracache_System_Tools_Uri::fixIndexUri($originalUri);
		$this->assertEquals($originalUri, $uri);
	}
}