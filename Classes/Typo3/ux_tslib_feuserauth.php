<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 AOE media GmbH <dev@aoemedia.de>
*  All rights reserved
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

// under certain circumstances it can happen, that the tslib_feUserAuth-class is unknown, so we must require them
if(class_exists('tslib_feUserAuth') === FALSE) {
	// do not use PATH_tslib: this constant is not available in every 'TYPO3-BE-context'
	require_once  t3lib_extMgm::extPath('cms') . 'tslib/class.tslib_feuserauth.php';
}

/**
 * Enhances the functionality of the user authentication.
 * 
 * @package extracache
 * @subpackage Typo3
 */
class ux_tslib_feUserAuth extends tslib_feUserAuth {
	/**
	 * Determines whether the frontend user was initialized (start() method called).
	 * @var boolean
	 */
	protected $isInitialized = FALSE;

	/**
	 * @return	void
	 */
	public function start() {
		$this->isInitialized = TRUE;
		parent::start();
	}

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
	 * Determines whether the frontend user was initialized (start() method called).
	 *
	 * @return boolean
	 */
	public function isInitialized() {
		return $this->isInitialized;
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

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['tslib/class.ux_tslib_feuserauth.php'])	{
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['tslib/class.ux_tslib_feuserauth.php']);
}