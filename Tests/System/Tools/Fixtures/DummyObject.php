<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2010 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * @package extracache_tests
 * @subpackage System_Tools_Fixtures
 */
class Tx_Extracache_System_Tools_Fixtures_DummyObject {
	/**
	 * @var string
	 */
	public $name;

	/**
	 * @param	integer $value1
	 * @param	integer $value2
	 * @param 	integer $value3
	 * @return	integer
	 */
	public function calculate($value1, $value2, $value3) {
		return $value1+$value2+$value3;
	}
	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}
}