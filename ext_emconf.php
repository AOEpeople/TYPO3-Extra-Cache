<?php
########################################################################
# Extension Manager/Repository config file for ext "extracache".
#
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'extracache',
	'description' => 'Extend static cache with more functionality',
	'category' => '',
	'author' => '',
	'author_email' => 'dev@aoe.com',
	'author_company' => 'AOE GmbH',
	'shy' => '',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'version' => '0.9.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '6.2.0-6.2.99',
			'extbase' => '',
			'nc_staticfilecache' => ''
		),
		'conflicts' => array(),
		'suggests' => array(),
	),
	'_md5_values_when_last_written' => '',
);
