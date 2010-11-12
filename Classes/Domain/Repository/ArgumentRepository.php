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
 */
class Tx_Extracache_Domain_Repository_ArgumentRepository implements t3lib_Singleton {
	/**
	 * @var array
	 */
	private $arguments;
	
	/**
	 * @param Tx_Extracache_Domain_Model_Argument $argument
	 */
	public function addArgument(Tx_Extracache_Domain_Model_Argument $argument) {
		$this->arguments[] = $argument;
	}
	/**
	 * @param	string $type
	 * @return	array
	 * @throws	LogicException
	 */
	public function getArgumentsByType($type) {
		if(!in_array($type, Tx_Extracache_Domain_Model_Argument::getSupportedTypes())) {
			throw new LogicException('type '.$type.' is not supported!');
		}
		
		$arguments = array();
		foreach($this->arguments as $argument) {
			if($argument->getType() === $type) {
				$arguments[] = $argument;
			}
		}
		return $arguments;
	}
}