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
 * @subpackage System_Persistence
 */
class Tx_Extracache_System_Persistence_Typo3DbBackend {
	/**
	 * @param	string $eventKey
	 * @return	array
	 */
	public function getPagesWithCacheCleanerStrategyForEvent($eventKey) {
		$sqlSelect = 'uid,title,tx_extracache_cleanerstrategies';
		$sqlFrom   = 'pages';
		$sqlWhere  = "tx_extracache_cleanerstrategies !='' AND (tx_extracache_events = '".$eventKey."' OR tx_extracache_events like '".$eventKey.",%' OR tx_extracache_events like '%,".$eventKey."' OR tx_extracache_events like '%,".$eventKey.",%')" . self::getSqlWherePartForPagesWithCacheCleanerStrategy();
		$sqlOrderBy = 'pid ASC, title ASC';

		if(FALSE != $data = $this->selectQuery($sqlSelect, $sqlFrom, $sqlWhere, $sqlOrderBy)) {
			return $data;
		}
		return array();
	}
	/**
	 * @param	integer $pageId
	 * @return	array
	 */
	public function getPageWithCacheCleanerStrategyForPageId($pageId) {
		$sqlSelect = 'uid,title,tx_extracache_cleanerstrategies';
		$sqlFrom   = 'pages';
		$sqlWhere  = "tx_extracache_cleanerstrategies !='' AND uid=" . $pageId . self::getSqlWherePartForPagesWithCacheCleanerStrategy();

		if(FALSE != $data = $this->selectQuery($sqlSelect, $sqlFrom, $sqlWhere)) {
			return $data[0];
		}
	}
	/**
	 * @return string
	 */
	public static function getSqlWherePartForPagesWithCacheCleanerStrategy() {
		return ' AND deleted=0 AND hidden=0 AND doktype < 199 AND pid > 0';
	}
	/**
	 * @param	string $list
	 * @return	string
	 */
	public function cleanIntList($list) {
		return $this->getTypo3Db()->cleanIntList( $list );
	}
	
	/**
	 * @param string $sqlFrom
	 * @param string $sqlWhere
	 */
	public function deleteQuery($sqlFrom, $sqlWhere) {
		$this->getTypo3Db()->exec_DELETEquery ( $sqlFrom, $sqlWhere );
	}
	/**
	 * @param string $table
	 * @param array $values
	 */
	public function insertQuery($table, array $values) {
		$this->getTypo3Db()->exec_INSERTquery($table, $values);
	}
	/**
	 * @param string $sqlFrom
	 * @param string $sqlWhere
	 * @param array $modifiedValues
	 */
	public function updateQuery($sqlFrom, $sqlWhere, $modifiedValues) {
		$this->getTypo3Db()->exec_UPDATEquery ( $sqlFrom, $sqlWhere, $modifiedValues );
	}
	/**
	 * @param string $sqlSelect
	 * @param string $sqlFrom
	 * @param string $sqlWhere
	 * @param string $sqlOrderBy
	 * @param string $sqlLimit
	 * @return array
	 */
	public function selectQuery($sqlSelect, $sqlFrom, $sqlWhere='', $sqlOrderBy='', $sqlLimit='') {
		return $this->getTypo3Db()->exec_SELECTgetRows ( $sqlSelect, $sqlFrom, $sqlWhere, '', $sqlOrderBy, $sqlLimit );
	}

	/**
	 * returns the error number from the last MySQL function, or 0 (zero) if no error occurred.
	 * @return int
	 */
	public function getSqlErrorNumber() {
		return $this->getTypo3Db()->sql_errno();
	}

	/**
	 * Returns the error text from the last MySQL function, or '' (empty string) if no error occurred.
	 * @return string
	 */
	public function getSqlErrorText() {
		return $this->getTypo3Db()->sql_error();
	}

	/**
	* Get number of affected rows in previous MySQL operation
	* @return int the number of affected rows on success, and -1 if the last query failed.
	*/
	public function getAffectedRows() {
		return $this->getTypo3Db()->sql_affected_rows();
	}

	/**
	 * Escaping and quoting values for SQL statements.
	 *
	 * @param	string $str
	 * @param	string $table
	 * @return	string
	 */
	public function fullQuoteStr($str, $table) {
		return $this->getTypo3Db()->fullQuoteStr($str, $table);
	}
	/**
	 * @param Tx_Extracache_Domain_Model_EventLog $eventLog
	 */
	public function writeEventLog(Tx_Extracache_Domain_Model_EventLog $eventLog) {
		$values = array();
		$values['event_key'] = $eventLog->getEvent()->getKey();
		$values['start_time'] = $eventLog->getStartTime();
		$values['stop_time'] = $eventLog->getStopTime();
		$values['infos'] = serialize( $eventLog->getInfos() );
		$this->insertQuery('tx_extracache_eventlog', $values);
	}

	/**
	 * @return	t3lib_DB
	 * @throws	RuntimeException
	 */
	protected function getTypo3Db() {
		global $TYPO3_DB;
		
		// create link if link doesn't exist
		if($TYPO3_DB->link === FALSE) {
			if (!(
					TYPO3_db_host && TYPO3_db_username && TYPO3_db_password && TYPO3_db &&
					$TYPO3_DB->sql_pconnect(TYPO3_db_host, TYPO3_db_username, TYPO3_db_password) &&
					$TYPO3_DB->sql_select_db(TYPO3_db)
			)) {
				throw new RuntimeException('Could not connect to TYPO3 database.');
			}
		}
		
		return $TYPO3_DB;
	}
}