<style type="text/css">
<!--
	table td.nowrap		{ white-space:nowrap; }
	table.lrPadding td	{ padding-left: 5px; padding-right: 5px; }
-->
</style>


<h2>Alle Dateien (<?php echo count($GLOBALS['view_data']['allFiles']). ' Einträge';?>):</h2>
<table border="0" cellspacing="1" class="lrPadding" width="100%">
	<tr class="bgColor5 tableheader">
		
		<th>Name</th>
		<th>Aktion</th>
	</tr>

<?php 
foreach($GLOBALS['view_data']['allFiles'] as $file){
	?>
	<tr class="bgColor4">
		<td class="nowrap"><?php echo $file->getName(); ?></td>
		<td class="nowrap"><a href="?action=deleteFile&id=<?php echo $file->getIdentifier(); ?>">L&ouml;schen</a></td>
	</tr>
	<?php 
}
?>
</table>