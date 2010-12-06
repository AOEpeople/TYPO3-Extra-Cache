<style type="text/css">
<!--
	table td.nowrap		{ white-space:nowrap; }
	table.lrPadding td	{ padding-left: 5px; padding-right: 5px; }
-->
</style>


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
<br /><br />


<h2><?php echo $GLOBALS['LANG']->getLL('headline_allDirectories').' ('.count($GLOBALS['view_data']['allFolders']).' '.$GLOBALS['LANG']->getLL('entries').'):';?></h2>
<table border="0" cellspacing="1" class="lrPadding" width="100%">
	<tr class="bgColor5 tableheader">
		<th><?php echo $GLOBALS['LANG']->getLL('headline_entityName');?></th>
		<th><?php echo $GLOBALS['LANG']->getLL('headline_entityAction');?></th>
	</tr>

<?php 
foreach($GLOBALS['view_data']['allFolders'] as $file){
	?>
	<tr class="bgColor4">
		<td class="nowrap"><?php echo $file->getName(); ?></td>
		<td class="nowrap"><a href="?action=deleteFolder&id=<?php echo $file->getIdentifier(); ?>"><?php echo $GLOBALS['LANG']->getLL('entityActionDelete');?></a></td>
	</tr>
	<?php 
}
?>
</table>