<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

define ( 'PATH_tx_extracache', t3lib_extMgm::extPath ( 'extracache' ) );

// start bootstrap
require_once(PATH_tx_extracache . 'Classes/Bootstrap.php');
Bootstrap::start();