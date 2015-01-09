<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// add new columns to table 'pages'
$tempColumns = array(
	'tx_extracache_cleanerstrategies' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:extracache/Resources/Private/Language/locallang_db.xml:pages.tx_extracache_cleanerstrategies',
		'config' => array (
			'type' => 'select',
			'size' => 10,
			'minitems' => 0,
			'maxitems' => 10,
			'multiple' => FALSE,
			'items' => array(),
			'itemsProcFunc' => 'tx_Extracache_Typo3_UserFunc_CleanerStrategy->getCleanerStrategyItemsProcFunc',
		)
	),
	'tx_extracache_events' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:extracache/Resources/Private/Language/locallang_db.xml:pages.tx_extracache_events',
		'config' => array (
			'type' => 'select',
			'size' => 10,
			'minitems' => 0,
			'maxitems' => 10,
			'multiple' => FALSE,
			'items' => array(),
			'itemsProcFunc' => 'tx_Extracache_Typo3_UserFunc_Event->getEventItemsProcFunc',
		)
	),
);

t3lib_div::loadTCA('pages');
t3lib_extMgm::addTCAcolumns('pages',$tempColumns);
t3lib_extMgm::addToAllTCAtypes('pages','--div--;Cache,tx_extracache_cleanerstrategies,tx_extracache_events,tx_ncstaticfilecache_cache');

if (TYPO3_MODE == 'BE') {
	t3lib_extMgm::insertModuleFunction(
        'web_info',
        'tx_extracache_modfunc1',
        NULL,
        'LLL:EXT:extracache/Resources/Private/Language/locallang_db.xml:moduleFunction.tx_extracache_modfunc1'
    );
}