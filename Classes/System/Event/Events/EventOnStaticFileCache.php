<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 AOE media GmbH <dev@aoemedia.de>
*  All rights reserved
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * This event will be thrown whan static files are written or cleaned up.
 * 
 * @package extracache
 * @subpackage System_Event_Events
 */
class Tx_Extracache_System_Event_Events_EventOnStaticFileCache extends Tx_Extracache_System_Event_Events_Event {
	/**
	 * @var tx_ncstaticfilecache
	 */
	protected $parent;

	/**
	 * @var tslib_fe
	 */
	protected $frontend;

	/**
	 * Constructs this event.
	 *
	 * @param string $name
	 * @param object $contextObject
	 * @param array $infos
	 * @param tx_ncstaticfilecache $parent
	 * @param tslib_fe $frontend (optional)
	 */
	public function __construct($name, $contextObject, array $infos, tx_ncstaticfilecache $parent, tslib_fe $frontend = NULL) {
		parent::__construct($name, $contextObject, $infos);
		$this->parent = $parent;
		$this->frontend = $frontend;
	}

	/**
	 * Gets the parent.
	 *
	 * @return tx_ncstaticfilecache
	 */
	public function getParent() {
		return $this->parent;
	}

	/**
	 * Sets the parent.
	 *
	 * @param tx_ncstaticfilecache $parent
	 * @return void
	 */
	public function setParent($parent) {
		$this->parent = $parent;
	}

	/**
	 * Gets the frontend.
	 *
	 * @return tslib_fe
	 */
	public function getFrontend() {
		return $this->frontend;
	}

	/**
	 * Sets the frontend.
	 *
	 * @param tslib_fe $frontend
	 * @return void
	 */
	public function setFrontend(tslib_fe $frontend) {
		$this->frontend = $frontend;
	}
}