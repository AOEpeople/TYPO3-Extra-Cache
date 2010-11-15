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
 * @subpackage System_Event_Events
 */
class Tx_Extracache_System_Event_Events_Event {
	/**
	 * name of the event
	 *
	 * @var string
	 */
	protected $name;
	/**
	 * the contextObjet that raised the event
	 *
	 * @var object
	 */
	protected $contextObject;
	/**
	 * array with eventdata
	 *
	 * @var array
	 */
	protected $infos = array();
	/**
	 * flag if event is canceled
	 *
	 * @var boolean
	 */
	protected $canceled = FALSE;

	/**
	 * construct common event
	 *
	 * @param string $name
	 * @param object $contextObject
	 * @param array $infos
	 */
	public function __construct($name, $contextObject, array $infos) {
		$this->name = $name;
		$this->contextObject = $contextObject;
		$this->infos = $infos;
	}

	/**
	 * Returns the name of the event
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Returns the object of the event triggerer
	 *
	 * @return object
	 */
	public function getContextObject() {
		return $this->contextObject;
	}

	/**
	 * Returns the infos of the event
	 *
	 * @return array
	 */
	public function getInfos() {
		return $this->infos;
	}

	/**
	 * cancel the event
	 *
	 * @return void
	 */
	public function cancel() {
		$this->canceled = TRUE;
	}

	/**
	 * if the current event is canceled
	 *
	 * @return boolean
	 */
	public function isCanceled() {
		return $this->canceled;
	}
}