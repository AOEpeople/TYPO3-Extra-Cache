<!-- show from to define if database-overview should be presented -->
<form name="setConfigShowDatabaseDetails" action="index.php" method="GET">
	<?php
	$checked = '';
	if($GLOBALS['BE_USER']->getModuleData('tx_extracache_manager_showDatabaseDetails')) {
		$checked = ' checked="checked"';
	}
	?>
	<input type="hidden" name="action" value="setConfigShowDatabaseDetails" />
	<input onClick="javascript:this.form.submit();" type="checkbox" value="1" name="showDatabaseDetails" <?php echo $checked;?> />
	<?php echo $GLOBALS['LANG']->getLL('showDatabaseDetails');?>
</form>
<br />


<!-- show from to define if files-overview should be presented -->
<form name="setConfigshowFilesDetails" action="index.php" method="GET">
	<?php
	$checked = '';
	if($GLOBALS['BE_USER']->getModuleData('tx_extracache_manager_showFilesDetails')) {
		$checked = ' checked="checked"';
	}
	?>
	<input type="hidden" name="action" value="setConfigshowFilesDetails" />
	<input onClick="javascript:this.form.submit();" type="checkbox" value="1" name="showFilesDetails" <?php echo $checked;?> />
	<?php echo $GLOBALS['LANG']->getLL('showFilesDetails');?>
</form>
<br />


<!-- show database-overview -->
<?php
$countDatbaseEntrysForTableStaticCache = $countDatbaseEntrysForTablePages = $countDatbaseEntrysForTableEventqueue = '';
if($GLOBALS['view_data']['showDatabaseDetails'] === TRUE) {
	$countDatbaseEntrysForTableStaticCache = ' - '.$GLOBALS['LANG']->getLL('count').': '.$GLOBALS['view_data']['countDatbaseEntrysForTableStaticCache'];
	$countDatbaseEntrysForTablePages = ' - '.$GLOBALS['LANG']->getLL('count').': '.$GLOBALS['view_data']['countDatbaseEntrysForTablePages'];
	$countDatbaseEntrysForTableEventqueue = ' - '.$GLOBALS['LANG']->getLL('count').': '.$GLOBALS['view_data']['countDatbaseEntrysForTableEventqueue'];
}
?>
<h2><?php echo $GLOBALS['LANG']->getLL('headline_databaseentries');?></h2>
<ul>
	<li><a href="?action=allDatabaseEntrysForTableStaticCache"><?php echo $GLOBALS['LANG']->getLL('showAll').' ('.$GLOBALS['LANG']->getLL('table').' \''.$GLOBALS['view_data']['tableStaticCache'].'\''.$countDatbaseEntrysForTableStaticCache.')'; ?></a></li>
	<li><a href="?action=allDatabaseEntrysForTablePages"><?php echo $GLOBALS['LANG']->getLL('showAll').' ('.$GLOBALS['LANG']->getLL('table').' \''.$GLOBALS['view_data']['tablePages'].'\''.$countDatbaseEntrysForTablePages.')'; ?></a></li>
	<li><a href="?action=allDatabaseEntrysForTableEventqueue"><?php echo $GLOBALS['LANG']->getLL('showAll').' ('.$GLOBALS['LANG']->getLL('table').' \''.$GLOBALS['view_data']['tableEventQueue'].'\''.$countDatbaseEntrysForTableEventqueue.')'; ?></a></li>
</ul>


<!-- show files-overview -->
<h2><?php echo $GLOBALS['LANG']->getLL('headline_cachefiles');?></h2>
<?php
if($GLOBALS['view_data']['showFilesDetails'] === TRUE) {
	?>
	<p><?php echo $GLOBALS['LANG']->getLL('count').': '.$GLOBALS['view_data']['countFiles']; ?></p>
	<?php
} // END IF
?>
<ul>
	<li><a href="?action=allFiles"><?php echo $GLOBALS['LANG']->getLL('showAllFiles');?></a></li>
	<li><a href="?action=allFolders"><?php echo $GLOBALS['LANG']->getLL('showAllDirectories');?></a></li>
</ul>