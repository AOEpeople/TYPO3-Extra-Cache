<style type="text/css">
<!--
	table td.nowrap		{ white-space:nowrap; }
	table.lrPadding td	{ padding-left: 5px; padding-right: 5px; }
-->
</style>


<h2><?php echo $GLOBALS['LANG']->getLL('headline_allFiles').' ('.count($GLOBALS['view_data']['allFiles']).' '.$GLOBALS['LANG']->getLL('entries').'):';?></h2>
<table border="0" cellspacing="1" class="lrPadding" width="100%">
	<tr class="bgColor5 tableheader">
		<th><?php echo $GLOBALS['LANG']->getLL('headline_entityName');?></th>
		<th><?php echo $GLOBALS['LANG']->getLL('headline_entityAction');?></th>
	</tr>

<?php 
foreach($GLOBALS['view_data']['allFiles'] as $file){
	?>
	<tr class="bgColor4">
		<td class="nowrap"><?php echo $file->getName(); ?></td>
		<td class="nowrap"><a href="?action=deleteFile&id=<?php echo $file->getIdentifier(); ?>"><?php echo $GLOBALS['LANG']->getLL('entityActionDelete');?></a></td>
	</tr>
	<?php 
}
?>
</table>