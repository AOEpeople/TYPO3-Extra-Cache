<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 AOE media GmbH <dev@aoemedia.de>
*  All rights reserved
*
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * @package extracache
 * @subpackage System_Tools
 */
class Tx_Extracache_System_Tools_Uri {
	const FILE_Index = 'index.html';
	const TYPO3_Index = 'index.php';

	/**
	 * Ignores arguments in URI if defined in configuration.
	 *
	 * @param	string		$uri
	 * @param	array		$ignoreArguments
	 * @return	string		The filtered URI
	 */
	static public function filterUriArguments($uri, array $ignoreArguments) {
		$queryPartStart = strpos($uri, '?');
		
		$ignoreArgumentKeys = array();
		/* @var $ignoreArgument Tx_Extracache_Domain_Model_Argument */
		foreach ($ignoreArguments as $ignoreArgument) {
			if(!in_array($ignoreArgument->getName(), $ignoreArgumentKeys)) {
				$ignoreArgumentKeys[] = $ignoreArgument->getName();
			}
		}

		// Process the query arguments if there are some:
		if (count($ignoreArgumentKeys) && $queryPartStart !== false) {
			$arguments = t3lib_div::trimExplode('&', substr($uri, $queryPartStart + 1));
				// If all arguments shall be ignored, set the cleaned URI:
			if (self::canCleanAllArguments($arguments, $ignoreArgumentKeys)) {
				$uri = substr($uri, 0, strpos($uri, '?'));
			}
		}

		return $uri;
	}

	/**
	 * Fixes an URI pointing to TYPO3 index.php or index.php.something.
	 *
	 * @param	string		$uri The URI to be fixed
	 * @return	string		The fixed URI
	 */
	static public function fixIndexUri($uri) {
		if (self::isIndexUri($uri) && strpos($uri, '?') === false) {
			$uri = '/';
		}

		return $uri;
	}

	/**
	 * Determines whether an URI is pointing to TYPO3 index.php or index.php.something.
	 *
	 * @param	string		$uri The URI to be checked
	 * @return	boolean		Whether URI is pointing to TYPO3 index.php
	 */
	static public function isIndexUri($uri) {
		return (strpos($uri, self::TYPO3_Index) === 0 || strpos($uri, '/' . self::TYPO3_Index) === 0);
	}

	/**
	 * Determines whether no arguments remain after cleaning the arguments to be ignored.
	 *
	 * @param	array		$arguments Initial query arguments
	 * @param	array		$ignoreArguments Arguments to be ignored
	 * @return	boolean		Whether no arguments remain after cleaning
	 */
	static protected function canCleanAllArguments(array $arguments, array $ignoreArguments) {
		$result = true;

		foreach ($arguments as $argument) {
			if (!in_array(self::getArgumentName($argument), $ignoreArguments)) {
				$result = false;
				break;
			}
		}

		return $result;
	}

	/**
	 * Gets the argument name out of a query string like
	 * 'argumentName[subArgument]=13' or 'argumentName=13'
	 *
	 * @param	string		$argument
	 * @return	string
	 */
	static protected function getArgumentName($argument) {
		$assignmentStart = strpos($argument, '=');
		$subArgumentStart = strpos(str_replace('%5B', '[', $argument), '[');

		if ($subArgumentStart !== false) {
			$argument = substr($argument, 0, $subArgumentStart);
		} elseif ($assignmentStart !== false) {
			$argument = substr($argument, 0, $assignmentStart);
		}

		return $argument;
	}
}