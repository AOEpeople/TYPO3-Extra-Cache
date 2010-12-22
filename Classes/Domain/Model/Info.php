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
 * value-object, which defines an info
 * 
 * @package extracache
 */
class Tx_Extracache_Domain_Model_Info {
	const TYPE_exception = 'exception';
	const TYPE_notice = 'notice';

	/**
	 * @var integer
	 */
	private $timestamp;
	/**
	 * @var string
	 */
	private $title;
	/**
	 * @var string
	 */
	private $type;

	/**
	 * @param string $title
	 * @param string $type
	 */
	public function __construct($title, $type) {
		$this->timestamp = time();
		$this->title = $title;
		$this->type = $type;
	}

	/**
	 * @return integer
	 */
	public function getTimestamp() {
		return $this->timestamp;
	}
	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}
	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}
}