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

use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 * this event will be thrown, if the request could be respond by staticCacheDispatcher and the frontent was initializesd
 * 
 * @package extracache
 * @subpackage System_Event_Events
 */
class Tx_Extracache_System_Event_Events_EventOnInitializeFrontEnd extends Tx_Extracache_System_Event_Events_Event {
	/**
	 * name of the event
	 * @var string
	 */
	protected $name = 'onInitializeFrontEnd';
	/**
	 * @var FrontendUserAuthentication
	 */
	private $frontendUser;

	/**
	 * override constructor
	 */
	public function __construct(){}

	/**
	 * @return FrontendUserAuthentication
	 */
	public function getFrontendUser() {
		return $this->frontendUser;
	}

	/**
	 * @param	FrontendUserAuthentication $frontendUser
	 * @return	Tx_Extracache_System_Event_Events_EventOnStaticCacheRequest
	 */
	public function setFrontendUser(FrontendUserAuthentication $frontendUser) {
		$this->frontendUser = $frontendUser;
		return $this;
	}
}
