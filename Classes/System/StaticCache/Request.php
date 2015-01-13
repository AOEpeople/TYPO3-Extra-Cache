<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 AOE GmbH <dev@aoe.com>
*  All rights reserved
*
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * defines an staticCache-request
 *
 * @package extracache
 * @subpackage System_StaticCache
 */
class Tx_Extracache_System_StaticCache_Request implements \TYPO3\CMS\Core\SingletonInterface {
	/**
	 * @var array
	 */
	protected $arguments;
	/**
	 * @var array
	 */
	protected $cookies;
	/**
	 * @var string
	 */
	protected $fileName;
	/**
	 * @var string
	 */
	protected $fileNameWithQuery;
	/**
	 * @var string
	 */
	protected $hostName;
	/**
	 * @var array
	 */
	protected $serverVariables;

	/**
	 * constructor
	 */
	public function __construct() {
		$this->getCookies();
		$this->getArguments();
		$this->getServerVariables();
	}
	/**
	 * Gets a specific name of the current arguments.
	 *
	 * @param	string		$name The name of the argument to be fetched
	 * @return	mixed		Information of the argument or NULL if something went wrong
	 */
	public function getArgument($name) {
		$result = NULL;
		if (isset($this->arguments[$name])) {
			$result = $this->arguments[$name];
		}
		return $result;
	}
	/**
	 * Gets the submitted GET and POST arguments (POST overrides GET).
	 *
	 * @return	array		The submitted GET and POST arguments
	 */
	public function getArguments() {
		if (!isset($this->arguments)) {
            $this->arguments = array();
            ArrayUtility::mergeRecursiveWithOverrule($this->arguments, $_GET);
            ArrayUtility::mergeRecursiveWithOverrule($this->arguments, $_POST);
            GeneralUtility::stripSlashesOnArray($this->arguments);
		}
		return $this->arguments;
	}
	/**
	 * Gets a specific name of the current cookies.
	 *
	 * @param	string		$name The name of the cookie to be fetched
	 * @return	mixed		Information of the cookie or NULL if something went wrong
	 */
	public function getCookie($name) {
		$result = NULL;
		if (isset($this->cookies[$name])) {
			$result = $this->cookies[$name];
		}
		return $result;
	}
	/**
	 * Gets the submitted cookies.
	 *
	 * @return	array		The submitted cookies
	 */
	public function getCookies() {
		if (!isset($this->cookies)) {
			$this->cookies = $_COOKIE;
		}
		return $this->cookies;
	}
	/**
	 * Gets the file name of the current request.
	 *
	 * Request: "http://www.aoemedia.de:80/path/index.html?someArgument=1"
	 * Result: "/path/index.html"
	 *
	 * @return	string		The file name of the current request
	 */
	public function getFileName() {
		if (!isset($this->fileName)) {
			$this->fileName = $this->getFileNameWithQuery();
			$queryPosition = strpos($this->fileName, '?');
			if ($queryPosition !== false) {
				$this->fileName = substr($this->fileName, 0, $queryPosition);
			}
		}
		return $this->fileName;
	}
	/**
	 * Gets the file name of the current request with the query part.
	 *
	 * Request: "http://www.aoemedia.de:80/path/index.html?someArgument=1"
	 * Result: "/path/index.html?someArgument=1"
	 *
	 * @return	string		The file name of the current request
	 */
	public function getFileNameWithQuery() {
		if (!isset($this->fileNameWithQuery)) {
			$this->fileNameWithQuery = $this->getIndpEnvFromTypo3('TYPO3_SITE_SCRIPT');
		}
		return $this->fileNameWithQuery;
	}
	/**
	 * Gets the host name of the current request.
	 *
	 * Request: "http://www.aoemedia.de:80/path/index.html"
	 * Result: "www.aoemedia.de"
	 *
	 * @return	string		The host name of the current request
	 */
	public function getHostName() {
		if (!isset($this->hostName)) {
			$this->hostName = $this->getIndpEnvFromTypo3('TYPO3_HOST_ONLY');
		}
		return $this->hostName;
	}
	/**
	 * Gets the current server variables.
	 *
	 * @return	array		The current server variables
	 */
	public function getServerVariables() {
		if (!isset($this->serverVariables)) {
			$this->serverVariables = $_SERVER;
		}
		return $this->serverVariables;
	}
	/**
	 * Gets a specific name of the current server variables.
	 *
	 * @param	string		$name The name of the server variable to be fetched
	 * @return	mixed		Information of the server variable or NULL if something went wrong
	 */
	public function getServerVariable($name) {
		$result = NULL;
		if (isset($this->serverVariables[$name])) {
			$result = $this->serverVariables[$name];
		}
		return $result;
	}

	/**
	 * @param	string $getEnvName
	 * @return	string
	 */
	protected function getIndpEnvFromTypo3($getEnvName) {
		return GeneralUtility::getIndpEnv( $getEnvName );
	}
}