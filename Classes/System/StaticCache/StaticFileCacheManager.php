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
 * @subpackage System_StaticCache
 */
class Tx_Extracache_System_StaticCache_StaticFileCacheManager extends Tx_Extracache_System_StaticCache_AbstractManager {
	/**
	 * @return	string
	 */
	protected function getCachedFolder() {
		$cacheFolder = $this->getExtensionManager()->get('path_StaticFileCache');
		$cacheFolder = rtrim($cacheFolder, '/') . '/';
		return $cacheFolder;
	}
	/**
	 * Gets the cached representation of the current request.
	 *
	 * @return	string		The cached representation of the current request
	 */
	protected function getCachedRepresentation() {
		$cachedRepresentation = $this->getCachedFolder() . $this->getRequest()->getHostName() . '/';
		if($this->getExtensionManager()->isSupportForFeUsergroupsSet() === TRUE) {
			$cachedRepresentation .= $this->getCachedRepresentationGroupList() . '/';
		}

		$requestFileName = $this->getRequest()->getFileName();
		if ($requestFileName && !Tx_Extracache_System_Tools_Uri::isIndexUri($requestFileName) && substr($requestFileName, -1) != '/') {
			 $cachedRepresentation.= $requestFileName . '/' . Tx_Extracache_System_Tools_Uri::FILE_Index;
		} elseif (substr($requestFileName, -1) == '/') {
			$cachedRepresentation.= $requestFileName . Tx_Extracache_System_Tools_Uri::FILE_Index;
		} else {
			$cachedRepresentation.= Tx_Extracache_System_Tools_Uri::FILE_Index;
		}

		return $cachedRepresentation;
	}
}