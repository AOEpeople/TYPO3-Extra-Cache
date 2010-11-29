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
 * Implements an object proxy that instanciates the real object when it's required.
 * @package extracache
 * @subpackage System_Tools
 */
class Tx_Extracache_System_Tools_ObjectProxy {
	/**
	 * File resource of the class source code
	 * @var string
	 */
	protected $classFile;
	/**
	 * Name of the wrapped class
	 * @var string
	 */
	protected $className;
	/**
	 * Instance of the wrapped object (will be generated on demand)
	 * @var object
	 */
	protected $instance;
	/**
	 * The calling parent object
	 * @var object
	 */
	protected $parent;
	/**
	 * Name of a callback function of the parent object
	 * @var string
	 */
	protected $parentCallback;

	/**
	 * Constructs this object.
	 *
	 * @param	object		$parent The calling parent object
	 * @param	string		$className Name of the wrapped class
	 * @param	string		$classFile Path to the class file of the wrapped class
	 * @param	string		$parentCallback (optional) Name of a callback function of the parent object
	 *						that will be executed when the real object gets created
	 */
	public function __construct($parent, $className, $classFile, $parentCallback = '') {
		$this->parent = $parent;
		$this->className = $className;
		$this->classFile = $classFile;
		$this->parentCallback = $parentCallback;
	}

	/**
	 * Magic method that routes calls to the wrapped object.
	 *
	 * @param	string		$name Name of the called method
	 * @param	array		$arguments Arguments passed to the called method
	 * @return	mixed		The the result of the called method of the wrapped instance
	 */
	public function __call($name, array $arguments) {
		if (!$this->hasInstance()) {
			$this->initializeInstance();
		}

		$result = call_user_func_array(
			array($this->instance, $name),
			$arguments
		);

		return $result;
	}
	/**
	 * Magic method that forwards a get action to the class members of the wrapped object.
	 *
	 * @param	string		$name Name of the class member of the wrapped object
	 * @return	mixed		The value of the class member
	 */
	public function __get($name) {
		if (!$this->hasInstance()) {
			$this->initializeInstance();
		}

		$result = $this->instance->$name;

		return $result;
	}
	/**
	 * Magic method that forwards a set action to the class member of the wrapped object.
	 *
	 * @param	string		$name Name of the class member of the wrapped object
	 * @param	mixed		$value Value to be set to the class member
	 * @return	void
	 */
	public function __set($name, $value) {
		if (!$this->hasInstance()) {
			$this->initializeInstance();
		}

		$this->instance->$name = $value;
	}

	/**
	 * Determines whether the instance of the real object was already created.
	 *
	 * @return	boolean		Whether the instance of the real object exists
	 */
	protected function hasInstance() {
		return (isset($this->instance));
	}
	/**
	 * Initializes the real object of this proxy.
	 *
	 * @return	void
	 */
	protected function initializeInstance() {
			// Load and create the object if provided:
		if ($this->classFile && $this->className) {
			require_once $this->classFile;
			$this->instance = t3lib_div::makeInstance($this->className);
		}
			// Perform a callback (that might also load required files etc.):
		if ($this->parentCallback) {
			call_user_func_array(
				array($this->parent, $this->parentCallback),
				array(&$this->instance)
			);
		}
	}
}