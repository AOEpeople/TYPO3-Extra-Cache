<style type="text/css">
<!--
	table td.nowrap		{ white-space:nowrap; }
	table.lrPadding td	{ padding-left: 5px; padding-right: 5px; }
-->
</style>


<!-- show from to define if empty folder should also be displayed -->
<h2><?php echo $GLOBALS['LANG']->getLL('headline_filter');?>:</h2>
<form name="setConfigGetFoldersWhichDoesNotContainFiles" action="index.php" method="GET">
	<?php
	$checked = '';
	if($GLOBALS['BE_USER']->getModuleData('tx_extracache_manager_getFoldersWhichDoesNotContainFiles') !== FALSE) {
		$checked = ' checked="checked"';
	}
	?>
	<input type="hidden" name="action" value="setConfigGetFoldersWhichDoesNotContainFiles" />
	<input onClick="javascript:this.form.submit();" type="checkbox" value="1" name="getFoldersWhichDoesNotContainFiles" <?php echo $checked;?> />
	<?php echo $GLOBALS['LANG']->getLL('showEmptyDirectories');?>
</form>
<br />
<!-- show from for folder-search -->
<form name="setConfigSearchPhraseForFolders" action="index.php" method="GET">
	<input type="hidden" name="action" value="setConfigSearchPhraseForFolders" />
	<input type="text" name="searchPhraseForFolders" value="<?php echo $GLOBALS['BE_USER']->getModuleData('tx_extracache_manager_searchPhraseForFolders'); ?>" />
	<input onClick="javascript:this.form.submit();" type="button" value="<?php echo $GLOBALS['LANG']->getLL('startFolderSearch');?>" />
</form>
<br /><br />


<!-- show folder -->
<h2><?php echo $GLOBALS['LANG']->getLL('headline_allDirectories').' ('.count($GLOBALS['view_data']['allFolders']).' '.$GLOBALS['LANG']->getLL('entries').'):';?></h2>
<table border="0" cellspacing="1" class="lrPadding" width="100%">
	<tr class="bgColor5 tableheader">
		<th><?php echo $GLOBALS['LANG']->getLL('headline_entityName');?></th>
		<th><?php echo $GLOBALS['LANG']->getLL('headline_entityModificationTime');?></th>
		<th><?php echo $GLOBALS['LANG']->getLL('headline_entityAction');?></th>
	</tr>

<?php 
foreach($GLOBALS['view_data']['allFolders'] as $file){
	?>
	<tr class="bgColor4">
		<td class="nowrap"><?php echo $file->getName(); ?></td>
		<td class="nowrap"><?php echo t3lib_BEfunc::datetime($file->getLastModificationTime()); ?></td>
		<td class="nowrap"><a href="?action=deleteFolder&id=<?php echo $file->getIdentifier(); ?>"><?php echo $GLOBALS['LANG']->getLL('entityActionDelete');?></a></td>
	</tr>
	<?php 
}
?>
</table>