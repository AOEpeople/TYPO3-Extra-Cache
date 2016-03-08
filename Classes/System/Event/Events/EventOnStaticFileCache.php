<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 AOE media GmbH <dev@aoemedia.de>
*  All rights reserved
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * This event will be thrown when static files are written or cleaned up in:
 *  - Tx_Extracache_Typo3_Hooks_StaticFileCache_AbstractHook
 * 
 * @package extracache
 * @subpackage System_Event_Events
 */
class Tx_Extracache_System_Event_Events_EventOnStaticFileCache extends Tx_Extracache_System_Event_Events_Event {
	/**
	 * @var TypoScriptFrontendController
	 */
	private $frontend;
	/**
	 * @var tx_ncstaticfilecache
	 */
	private $parent;

	/**
	 * Constructs this event.
	 *
	 * @param string $name
	 * @param object $contextObject
	 * @param array $infos
	 * @param tx_ncstaticfilecache $parent
	 * @param TypoScriptFrontendController $frontend (optional)
	 */
	public function __construct($name, $contextObject, array $infos, tx_ncstaticfilecache $parent, TypoScriptFrontendController $frontend = NULL) {
		parent::__construct($name, $contextObject, $infos);
		
		$this->setParent( $parent );
		if($frontend !== NULL) {
			$this->setFrontend( $frontend );
		}
	}
	/**
	 * @return TypoScriptFrontendController
	 */
	public function getFrontend() {
		return $this->frontend;
	}
	/**
	 * @return tx_ncstaticfilecache
	 */
	public function getParent() {
		return $this->parent;
	}

	/**
	 * @param TypoScriptFrontendController $frontend
	 * @return void
	 */
	private function setFrontend(TypoScriptFrontendController $frontend) {
		$this->frontend = $frontend;
	}
	/**
	 * @param tx_ncstaticfilecache $parent
	 * @return void
	 */
	private function setParent(tx_ncstaticfilecache $parent) {
		$this->parent = $parent;
	}
}