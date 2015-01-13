<style type="text/css">
<!--
	table td.nowrap		{ white-space:nowrap; }
	table.lrPadding td	{ padding-left: 5px; padding-right: 5px; }
-->
</style>


<!-- show from for files-search -->
<h2><?php echo $GLOBALS['LANG']->getLL('headline_filter');?>:</h2>
<form name="setConfigSearchPhraseForFiles" action="index.php" method="GET">
	<input type="hidden" name="action" value="setConfigSearchPhraseForFiles" />
	<input type="text" name="searchPhraseForFiles" value="<?php echo $GLOBALS['BE_USER']->getModuleData('tx_extracache_manager_searchPhraseForFiles'); ?>" />
	<input onClick="javascript:this.form.submit();" type="button" value="<?php echo $GLOBALS['LANG']->getLL('startFilesSearch');?>" />
</form>
<br /><br /><br />


<!-- show files -->
<h2><?php echo $GLOBALS['LANG']->getLL('headline_allFiles').' ('.count($GLOBALS['view_data']['allFiles']).' '.$GLOBALS['LANG']->getLL('entries').'):';?></h2>
<table border="0" cellspacing="1" class="lrPadding" width="100%">
	<tr class="bgColor5 tableheader">
		<th><?php echo $GLOBALS['LANG']->getLL('headline_entityName');?></th>
		<th><?php echo $GLOBALS['LANG']->getLL('headline_entityModificationTime');?></th>
		<th><?php echo $GLOBALS['LANG']->getLL('headline_entityAction');?></th>
	</tr>

<?php 
foreach($GLOBALS['view_data']['allFiles'] as $file){
	?>
	<tr class="bgColor4">
		<td class="nowrap"><?php echo $file->getName(); ?></td>
		<td class="nowrap"><?php echo \TYPO3\CMS\Backend\Utility\BackendUtility::datetime($file->getLastModificationTime()); ?></td>
		<td class="nowrap"><a href="?action=deleteFile&id=<?php echo $file->getIdentifier(); ?>"><?php echo $GLOBALS['LANG']->getLL('entityActionDelete');?></a></td>
	</tr>
	<?php 
}
?>
</table>