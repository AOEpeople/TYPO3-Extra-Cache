<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2014 AOE GmbH <dev@aoe.com>
*  All rights reserved
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Enhances the functionality of the user authentication.
 *
 * @package extracache
 * @subpackage Typo3
 */
class Tx_Extracache_Xclass_FrontendUserAuthentication extends \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication {
	/**
	 * Determines whether this is a valid frontend user with
	 * accordant data and user groups assigned.
	 *
	 * @return boolean
	 */
	public function isValidFrontendUser() {
		return (is_array($this->user) && count($this->groupData['uid']));
	}

	/**
	 * Gets the frontend user group list.
	 * CAVE: The anonymous groups (0,-1) and (0,-2) are already prepended!
	 *
	 * @return string
	 */
	public function getGroupList() {
		if ($this->isValidFrontendUser()) {
			$frontendUserGroups = array_unique($this->groupData['uid']);
			sort($frontendUserGroups);
			$frontendUserGroupList = '0,-2,' . implode(',', $frontendUserGroups);
		} else {
			$frontendUserGroupList = '0,-1';
		}

		return $frontendUserGroupList;
	}
}