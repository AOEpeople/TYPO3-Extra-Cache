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
	 * @return array
	 */
	public function getAll() {
		return $this->reconstitute ( $this->getFiles () );
	}
	/**
	 * @param	boolean $getFoldersWhichDoesNotContainFiles get folders, which doesn't contain any files
	 * @return	array
	 */
	public function getAllFolders($getFoldersWhichDoesNotContainFiles = TRUE) {
		$files = $this->getFolders ();
		$folders = array ();
		foreach ( $files as $file ) {
			$name = '';
			if ($getFoldersWhichDoesNotContainFiles === TRUE && $file->isDir ()) {
				$name = $this->replacePath ( $file->getPathname() );
			} elseif ($getFoldersWhichDoesNotContainFiles === FALSE && $file->isFile ()) {
				$name = $this->replacePath ( $file->getPath() );
			}
			if (! isset ( $folders [$name] ) && '' !== $name) {
				$cacheFile = new Tx_Extracache_Domain_Model_CacheFile ();
				$cacheFile->setName ( $name );
				$folders [$name] = $cacheFile;
			}
		}
		sort( $folders );
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
	 * @param Iterator $regexIterator
	 * @return array
	 */
	private function reconstitute(Iterator $regexIterator) {
		$files = array ();
		foreach ( $regexIterator as $fileName => $file ) {
			$cacheFile = new Tx_Extracache_Domain_Model_CacheFile ();
			$cacheFile->setName ( $this->replacePath ( $fileName ) );
			$files [] = $cacheFile;
		}
		sort( $files );
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