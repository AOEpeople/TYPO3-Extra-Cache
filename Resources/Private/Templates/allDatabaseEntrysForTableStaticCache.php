<?php
$defaultColumns = array('tstamp','crdate','cache_timeout','host','uri','file','uid','pid','isdirty','explanation','additionalhash');
$additionalColumns = array();
if(count($GLOBALS['view_data']['allDatabaseEntrysForTableStaticCache']) > 0) {
	$additionalColumns = array_diff($GLOBALS['view_data']['allDatabaseEntrysForTableStaticCache'][0]->getRecordKeys(), $defaultColumns);
	sort( $additionalColumns );
}
?>

<style type="text/css">
<!--
	table td.nowrap		{ white-space:nowrap; }
	table.lrPadding td	{ padding-left: 5px; padding-right: 5px; }
-->
</style>


<h2>Alle Datenbankeinträge (<?php echo count($GLOBALS['view_data']['allDatabaseEntrysForTableStaticCache']). ' Einträge';?>):</h2>
<table border="0" cellspacing="1" class="lrPadding" width="100%">
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
	foreach($GLOBALS['view_data']['allDatabaseEntrysForTableStaticCache'] as $databaseEntry){
		$timeout = ($databaseEntry->getTstamp() > 0) ? t3lib_BEfunc::calcAge(($databaseEntry->getCache_timeout()),$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:labels.minutesHoursDaysYears')) : '';
		?>
		<tr class="bgColor4">
			<td class="nowrap"><?php echo date('d.m.Y H:i:s',$databaseEntry->getTstamp()); ?></td>
			<td class="nowrap"><?php echo date('d.m.Y H:i:s',$databaseEntry->getCrdate()); ?></td>
			<td class="nowrap"><?php echo $timeout; ?></td>
			<td class="nowrap"><?php echo $databaseEntry->getHost(); ?></td>
			<td class="nowrap"><?php echo $databaseEntry->getUri(); ?></td>
			<td class="nowrap"><?php echo $databaseEntry->getFile(); ?></td>
			<td class="nowrap"><?php echo $databaseEntry->getUid(); ?></td>
			<td class="nowrap"><?php echo $databaseEntry->getPid(); ?></td>
			<td class="nowrap"><?php echo $databaseEntry->getIsdirty(); ?></td>
			<td class="nowrap"><?php echo $databaseEntry->getExplanation(); ?></td>
			<td class="nowrap"><?php echo $databaseEntry->getAdditionalhash(); ?></td>
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