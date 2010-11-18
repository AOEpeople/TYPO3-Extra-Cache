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
 * Hook to determine, block and re-queue modifications concerning file references.
 * This is required in combination with statically cached files.
 * 
 * @package extracache
 * @subpackage Typo3_Hooks
 */
class Tx_Extracache_Typo3_Hooks_FileReferenceModification {
	const TABLE_Queue = 'tx_extracache_fileremovequeue';

	/**
	 * @var	array
	 */
	protected $filesToBeRemoved = array();
	/**
	 * @var	boolean
	 */
	protected $isStaticCacheEnabled;
	/**
	 * @var Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	private $typo3DbBackend;

	/**
	 * Processes a record after it was stored to the database.
	 *
	 * @param	string			$status
	 * @param	string			$table
	 * @param	mixed			$id
	 * @param	array			$fieldArray
	 * @param	t3lib_TCEmain	$parent
	 * @return	void
	 */
	public function processDatamap_afterDatabaseOperations($status, $table, $id, array $fieldArray, t3lib_TCEmain $parent) {
		if ($this->isStaticCacheEnabled() && is_array($parent->removeFilesStore) && count($parent->removeFilesStore)) {
			$this->filesToBeRemoved = array_unique(
				array_merge(
					$this->filesToBeRemoved,
					$parent->removeFilesStore
				)
			);

			$parent->removeFilesStore = array();
		}
	}
	/**
	 * Processes any storing action after all data has been persisted.
	 *
	 * @param	t3lib_TCEmain	$parent
	 * @return	void
	 */
	public function processDatamap_afterAllOperations(t3lib_TCEmain $parent) {
		if ($this->isStaticCacheEnabled() && count($this->filesToBeRemoved)) {
			$this->addRemovedFilesToQueue(
				$this->getRelativeFiles($this->filesToBeRemoved)
			);

			$this->filesToBeRemoved = array();
		}
	}

	/**
	 * Adds the files to be removed to the queue that will be processed later on.
	 *
	 * @param	array		$files Files to be removed
	 * @return	void
	 */
	protected function addRemovedFilesToQueue(array $files) {
		$fields = array(
			'tstamp' => $GLOBALS['EXEC_TIME'],
			'files' => implode(',', $files),
		);
		$this->getTypo3DbBackend()->insertQuery(self::TABLE_Queue, $fields);
	}
	/**
	 * Gets files referenced with a relative path instead of using an absolute path.
	 *
	 * @param	array		$files Files to get relative path for
	 * @return	array		Files with relative path below the TYPO3 site path
	 */
	protected function getRelativeFiles(array $files) {
		foreach ($files as &$file) {
			$file = $this->getRelativePath($file);
		}
		return $files;
	}
	/**
	 * Gets the relative path for a file instead of using the absolute path.
	 *
	 * @param	string		$file File to get relative path for
	 * @return	string		File with relative path below the TYPO3 site path
	 */
	protected function getRelativePath($file) {
		if (strpos($file, PATH_site) === 0) {
			$file = substr($file, strlen(PATH_site));
		}
		return $file;
	}
	/**
	 * @return Tx_Extracache_System_Persistence_Typo3DbBackend
	 */
	protected function getTypo3DbBackend() {
		if($this->typo3DbBackend === NULL) {
			$this->typo3DbBackend = t3lib_div::makeInstance('Tx_Extracache_System_Persistence_Typo3DbBackend');
		}
		return $this->typo3DbBackend;
	}

	/**
	 * Determines whether static caching is enabled.
	 *
	 * @return	boolean		Whether static caching is enabled
	 */
	protected function isStaticCacheEnabled() {
		if (!isset($this->isStaticCacheEnabled)) {
			$this->isStaticCacheEnabled = (bool) t3lib_div::makeInstance('Tx_Extracache_Configuration_ExtensionManager')->get('enableStaticCacheManager');
		}

		return $this->isStaticCacheEnabled;
	}
}