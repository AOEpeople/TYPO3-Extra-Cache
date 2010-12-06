<?php
$defaultColumns = array('first_called_time','status', 'event_key','event_interval');
$additionalColumns = array();
if(count($GLOBALS['view_data']['allDatabaseEntrysForTableEventqueue']) > 0) {
	$additionalColumns = array_diff($GLOBALS['view_data']['allDatabaseEntrysForTableEventqueue'][0]->getRecordKeys(), $defaultColumns);
	sort( $additionalColumns );
}
?>

<style type="text/css">
<!--
	table td.nowrap		{ white-space:nowrap; }
	table.lrPadding td	{ padding-left: 5px; padding-right: 5px; }
-->
</style>


<h2><?php echo $GLOBALS['LANG']->getLL('headline_allDatabaseentries').' ('.count($GLOBALS['view_data']['allDatabaseEntrysForTableEventqueue']). ' '.$GLOBALS['LANG']->getLL('entries').'):';?></h2>
<table border="0" cellspacing="1" class="lrPadding" width="50%">
	<tr class="bgColor5 tableheader">
		<?php
		foreach ($defaultColumns as $defaultColumn) {
			echo '<th>'.$defaultColumn.'</th>';
		}
		foreach ($additionalColumns as $additionalColumn) {
			echo '<th>'.$additionalColumn.'</th>';
		}
		?>
	</tr>

	<?php 
	foreach($GLOBALS['view_data']['allDatabaseEntrysForTableEventqueue'] as $databaseEntry){
		?>
		<tr class="bgColor4">
			<td class="nowrap"><?php echo date('d.m.Y H:i:s',$databaseEntry->getFirst_called_time()); ?></td>
			<td class="nowrap"><?php echo $databaseEntry->getStatus(); ?></td>
			<td class="nowrap"><?php echo $databaseEntry->getEvent_key(); ?></td>
			<td class="nowrap"><?php echo $databaseEntry->getEvent_interval(); ?></td>
			<?php
			foreach ($additionalColumns as $additionalColumn) {
				$methodName = 'get'.ucfirst($additionalColumn);
				echo '<td class="nowrap">'.call_user_func(array($databaseEntry, $methodName)).'</td>';
			}
			?>
		</tr>
		<?php 
	}
	?>
</table>