<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2009 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR.'Model' . DIRECTORY_SEPARATOR . 'CacheDatabaseEntry.php';
/**
 * Database cache entry repository
 * @package extracache
 */
class Tx_Extracache_Domain_Repository_CacheDatabaseEntryRepository {
	/**
	 * @var string
	 */
	private $fileTable;
	/**
	 * @var string
	 */
	private $orderBy='';

	/**
	 * @param string $where
	 * @return integer
	 */
	public function count($where = ''){
		$db = $GLOBALS['TYPO3_DB'];
		return intval($db->exec_SELECTcountRows('*', $this->getFileTable(),$where));
	}
	/**
	 * @return integer
	 */
	public function countAll() {
		return $this->count();
	}
	/**
	 * @return array
	 */
	public function getAll() {
		return $this->query();
	}
	/**
	 * @return string
	 */
	public function getFileTable() {
		return $this->fileTable;
	}

	/**
	 * @param string $where
	 * @return array
	 */
	public function query($where = '1=1'){
		$db = $GLOBALS['TYPO3_DB'];
		$rows = $db->exec_SELECTgetRows('*', $this->getFileTable(), $where, '', $this->getOrderBy());
		$entries = array();
		foreach($rows as $row){
			$entries[] = $this->createCacheDatabaseEntry( $row );
		}
		return $entries;
	}

	/**
	 * @param string $fileTable
	 */
	public function setFileTable($fileTable) {
		$this->fileTable = $fileTable;
	}
	/**
	 * @param string $orderBy
	 */
	public function setOrderBy($orderBy) {
		$this->orderBy = $orderBy;
	}

	/**
	 * @return string
	 */
	protected function getOrderBy() {
		return $this->orderBy;
	}

	/**
	 * @param	array $row
	 * @return	Tx_Extracache_Domain_Model_CacheDatabaseEntry
	 */
	private function createCacheDatabaseEntry(array $row) {
		$entry = new Tx_Extracache_Domain_Model_CacheDatabaseEntry();
		$entry->setRecordKeys( array_keys($row) );
		foreach($row as $key => $value) {
			$methodName = 'set'.ucfirst($key);
			call_user_func(array($entry, $methodName), $value);
		}
		return $entry;
	}
}