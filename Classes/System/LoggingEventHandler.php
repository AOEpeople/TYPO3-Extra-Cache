<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 AOE GmbH <dev@aoe.com>
*  All rights reserved
*
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @package extracache
 * @subpackage System
 */
class Tx_Extracache_System_LoggingEventHandler implements \TYPO3\CMS\Core\SingletonInterface {
	// log-levels are same as they used by GeneralUtility::devlog
	const LOG_INFO = 0;
	const LOG_NOTICE = 1;
	const LOG_WARNING = 2;
	const LOG_FATAL_ERROR = 3;
	
	/**
	 * @var Tx_Extracache_Configuration_ExtensionManager
	 */
	private $extensionManager;

	/**
	 * @param Tx_Extracache_System_Event_Events_Event $event
	 */
	public function logFatalError(Tx_Extracache_System_Event_Events_Event $event) {
		$this->handle ( $event, self::LOG_FATAL_ERROR );
	}
	/**
	 * @param Tx_Extracache_System_Event_Events_Event $event
	 */
	public function logInfo(Tx_Extracache_System_Event_Events_Event $event) {
		$this->handle ( $event, self::LOG_INFO );
	}
	/**
	 * @param Tx_Extracache_System_Event_Events_Event $event
	 */
	public function logNotice(Tx_Extracache_System_Event_Events_Event $event) {
		$this->handle ( $event, self::LOG_NOTICE );
	}
	/**
	 * @param Tx_Extracache_System_Event_Events_Event $event
	 */
	public function logWarning(Tx_Extracache_System_Event_Events_Event $event) {
		$this->handle ( $event, self::LOG_WARNING );
	}

	/**
	 * @return Tx_Extracache_Configuration_ExtensionManager
	 */
	protected function getExtensionManager() {
		if($this->extensionManager === NULL) {
			$this->extensionManager = GeneralUtility::makeInstance('Tx_Extracache_Configuration_ExtensionManager');
		}
		return $this->extensionManager;
	}
	/**
	 * @param string	$message
	 * @param integer	$severity
	 */
	protected function logMessage($message,$severity) {
		$exception = new Exception();
		$additional = array();
		if (isset($_SERVER['HTTP_REFERER'])) {
			$additional ['httpReferer'] = $_SERVER['HTTP_REFERER'];
		}
		if (isset($_SERVER['REQUEST_URI'])) {
			$additional ['requestUrl'] = $_SERVER['REQUEST_URI'];
		}
		$additional['debug_backtrace'] = $exception->getTraceAsString();
        GeneralUtility::devlog($message, 'extracache', $severity, $additional);
	}

	/**
	 * Handles events and puts accordant messages to the logs.
	 *
	 * @param	Tx_Extracache_System_Event_Events_Event	$event
	 * @return	void
	 */
	private function handle(Tx_Extracache_System_Event_Events_Event $event, $severity) {
		$message = $this->getMessage($event);
		if ($message !== NULL && ($this->getExtensionManager()->isDevelopmentContextSet() === TRUE || $severity >= self::LOG_WARNING)) {
			$this->logMessage($message, $severity);
		}
	}
	/**
	 * @param	Tx_Extracache_System_Event_Events_Event $event
	 * @return	string
	 */
	private function getMessage(Tx_Extracache_System_Event_Events_Event $event) {
		$message = NULL;
		$infos = $event->getInfos();
		if(isset($infos['message']) && !empty($infos['message'])) {
			$message = trim($infos['message']);
		}
		return $message;
	}
}	