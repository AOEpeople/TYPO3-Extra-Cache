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
require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Model' . DIRECTORY_SEPARATOR . 'CacheFile.php';
/**
 * file cache repository
 * @package extracache
 */
class Tx_Extracache_Domain_Repository_CacheFileRepository {
	/**
	 * @var string
	 */
	private $cacheDir;
	/**
	 * @return integer
	 */
	public function countAll() {
		return $this->count ( $this->getFiles () );
	}
	
	/**
	 * @param	string $searchPhrase
	 * @return	array
	 */
	public function getAll($searchPhrase) {
		return $this->reconstitute ( $this->getFiles (), $searchPhrase );
	}
	/**
	 * @param	boolean $getFoldersWhichDoesNotContainFiles get folders, which doesn't contain any files
	 * @param	string	$searchPhrase
	 * @return	array
	 */
	public function getAllFolders($getFoldersWhichDoesNotContainFiles, $searchPhrase) {
		$files = $this->getFolders ();
		$folders = array ();
		$tmpFolderNames = array();
		foreach ( $files as $file ) {
			// get name
			$name = '';
			if ($getFoldersWhichDoesNotContainFiles === TRUE && $file->isDir ()) {
				$name = $this->replacePath ( $file->getPathname() );
				$path = $file->getPathname();
			} elseif ($getFoldersWhichDoesNotContainFiles === FALSE && $file->isFile ()) {
				$name = $this->replacePath ( $file->getPath() );
				$path = $file->getPath();
			}

			// check name
			if ($name !== '' && ($searchPhrase === '' || strstr($name, $searchPhrase) !== FALSE) && isset ( $tmpFolderNames [$name] ) === FALSE) {
				$cacheFile = new Tx_Extracache_Domain_Model_CacheFile ();
				$cacheFile->setName ( $name );
				$cacheFile->setLastModificationTime( $this->getLastModificationTime( $path ));
				$folders [] = $cacheFile;
				$tmpFolderNames[$name] = $name;
			}
		}
		array_multisort($tmpFolderNames,SORT_ASC,$folders);
		return $folders;
	}
	/**
	 * @return string
	 */
	public function getCacheDir() {
		return $this->cacheDir;
	}
	/**
	 * @param string $id
	 */
	public function removeFile($id) {
		$fileName = base64_decode ( $id );
		if (FALSE === $fileName || FALSE !== strpos ( $fileName, '..' )) {
			throw new Exception ( 'invalid id' );
		}
		$path = $this->cacheDir . $fileName;
		if (is_file ( $path )) {
			if (FALSE === unlink ( $path )) {
				throw new Exception ( 'could not delete file: ' . $path );
			}
		} else {
			throw new Exception ( 'could not delete file: ' . $path );
		}
	}
	/**
	 * @param string $id
	 */
	public function removeFolder($id) {
		$fileName = base64_decode ( $id );
		if (FALSE === $fileName || FALSE !== strpos ( $fileName, '..' )) {
			throw new Exception ( 'invalid id: '.$id );
		}
		$path = $this->cacheDir . $fileName;
		if (FALSE === is_dir ( $path )) {
			throw new Exception ( 'path is not a folder: ' . $path );
		}
		$temp_path = $path . '_to_be_deleted';
		if (FALSE === rename ( $path, $temp_path )) {
			throw new Exception ( 'could not rename folder: ' . $path );
		}
		$dir = new RecursiveIteratorIterator ( new RecursiveDirectoryIterator ( $temp_path ), RecursiveIteratorIterator::CHILD_FIRST );
		for($dir->rewind (); $dir->valid (); $dir->next ()) {
			if ($dir->isDir ()) {
				if (FALSE === rmdir ( $dir->getPathname () )) {
					throw new Exception ( 'could not delete dir: ' . $dir->getPathname () );
				}
			} else {
				if (FALSE === unlink ( $dir->getPathname () )) {
					throw new Exception ( 'could not delete file: ' . $dir->getPathname () );
				}
			}
		}
		if (FALSE === rmdir ( $temp_path )) {
			throw new Exception ( 'could not delete dir: ' . $temp_path );
		}
	}
	/**
	 * @param string $cacheDir
	 */
	public function setCacheDir($cacheDir) {
		$this->cacheDir = $cacheDir;
	}

	/**
	 * get time of last modification
	 * 
	 * @param string $fileName
	 * @return integer
	 */
	private function getLastModificationTime($fileName) {
		$stat = stat($fileName);
		return $stat[9];
	}
	/**
	 * @param Iterator	$regexIterator
	 * @param string	$searchPhrase
	 * @return array
	 */
	private function reconstitute(Iterator $regexIterator, $searchPhrase) {
		$files = array ();
		$tmpFileNames = array();
		foreach ( $regexIterator as $fileName => $file ) {
			if($searchPhrase === '' || strstr($fileName, $searchPhrase) !== FALSE) {
				$cacheFile = new Tx_Extracache_Domain_Model_CacheFile ();
				$cacheFile->setLastModificationTime( $this->getLastModificationTime( $fileName ));
				$cacheFile->setName ( $this->replacePath ( $fileName ) );
				$files [] = $cacheFile;
				$tmpFileNames [] = $cacheFile->getName();
			}
		}
		array_multisort($tmpFileNames,SORT_ASC,$files);
		return $files;
	}	
	/**
	 * @param RegexIterator $regexIterator
	 * @return integer
	 */
	private function count(RegexIterator $regexIterator) {
		$count = 0;
		foreach ( $regexIterator as $file ) {
			$count ++;
		}
		return $count;
	}
	/**
	 * @return RegexIterator
	 */
	private function getFiles() {
		$folder = $this->cacheDir;
		$directory = new RecursiveDirectoryIterator ( $folder );
		$iterator = new RecursiveIteratorIterator ( $directory );
		$regex = new RegexIterator ( $iterator, '/^.+\.html$/i', RecursiveRegexIterator::GET_MATCH );
		return $regex;
	}
	/**
	 * @return RecursiveIteratorIterator
	 */
	private function getFolders() {
		$folder = $this->cacheDir;
		$objects = new RecursiveIteratorIterator ( new RecursiveDirectoryIterator ( $folder ), RecursiveIteratorIterator::SELF_FIRST );
		return $objects;
	}
	/**
	 * @param string $path
	 * @return string
	 */
	private function replacePath($path) {
		$replacedPath = str_replace ( substr ( $this->getCacheDir(), 0, strlen ( $this->getCacheDir() ) ), '', $path );
		if($replacedPath === $this->getCacheDir() || $replacedPath.'/' === $this->getCacheDir()) {
			return '';
		}
		return $replacedPath;
	}
}