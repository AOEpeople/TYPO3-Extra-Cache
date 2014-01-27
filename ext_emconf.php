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
	'description' => 'extend staticcache with more functionality',
	'category' => '',
	'author' => '',
	'author_email' => 'dev@aoemedia.de',
	'author_company' => 'AOE Media GmbH',
	'shy' => '',
	'dependencies' => 'cms,extbase,nc_staticfilecache',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'version' => '0.4.2',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'typo3' => '4.3.0',
			'php' => '5.2.0',
			'extbase' => '1.2.1',
			'nc_staticfilecache' => '2.3.4',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => '',
);
?>