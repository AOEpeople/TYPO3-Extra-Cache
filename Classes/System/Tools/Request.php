<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 AOE media GmbH <dev@aoemedia.de>
*  All rights reserved
*
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * @package extracache
 * @subpackage System_Tools
 */
class Tx_Extracache_System_Tools_Request {
	/**
	 * @param array $arguments
	 * @param array $unprocessibleRequestArguments
	 */
	public static function isUnprocessibleRequest($arguments, array $unprocessibleRequestArguments) {
		$result = false;

		/* @var $unprocessibleRequestArgument Tx_Extracache_Domain_Model_Argument */
		foreach ( $unprocessibleRequestArguments as $unprocessibleRequestArgument ) {
			$key = $unprocessibleRequestArgument->getName();
			$actions = $unprocessibleRequestArgument->getValue();

			if ($key === '*' && is_array ( $actions )) {
				foreach ( $arguments as $argumentValues ) {
					if (is_array ( $argumentValues )) {
						if (true === $result = Tx_Extracache_System_Tools_Request::getMatchedArguments ( $argumentValues, $actions )) {
							break;
						}
					}
				}
			} elseif (is_bool ( $actions ) && $actions) {
				$result = isset ( $arguments [$key] );
			} elseif (isset ( $arguments [$key] ) && is_array ( $arguments [$key] )=== TRUE && is_array ( $actions ) === TRUE) {
				$result = Tx_Extracache_System_Tools_Request::getMatchedArguments ( $arguments [$key], $actions );
			} elseif(isset ( $arguments [$key] ) && is_array ( $arguments [$key] ) === FALSE && is_array ( $actions ) === FALSE && $arguments [$key] === $actions) {
				$result = true;
			}

			if ($result) {
				break;
			}
		}

		return $result;
		
	}

	/**
	 * Gets the matches of the current request arguments concerning actions that shall be searched.
	 *
	 * @param	array		$arguments Current request arguments
	 * @param	array		$actions Action that shall be looked up in arguments
	 * @return	boolean		Whether there have been matches
	 */
	public static function getMatchedArguments(array $arguments, array $actions) {
		$result = false;

		$matches = array_intersect_key ( $arguments, $actions );
		if ($matches) {
			foreach ( $matches as $argumentSubKey => $argumentSubValue ) {
				if (is_array ( $actions [$argumentSubKey] ) && in_array ( $argumentSubValue, $actions [$argumentSubKey] ) || $actions [$argumentSubKey] === '*') {
					$result = true;
					break;
				}
			}
		}

		return $result;
	}
}