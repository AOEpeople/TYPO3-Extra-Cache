<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

define ( 'PATH_tx_extracache', t3lib_extMgm::extPath ( 'extracache' ) );

if (!defined('PATH_tslib')) {
	define('PATH_tslib', t3lib_extMgm::extPath('cms') . 'tslib/');
}

// start bootstrap
require_once(PATH_tx_extracache . 'Classes/Bootstrap.php');
Bootstrap::start();