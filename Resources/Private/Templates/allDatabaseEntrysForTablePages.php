<?php
$defaultColumns = array('uid', 'pid', 'title','tx_extracache_cleanerstrategies','tx_extracache_events', 'tx_ncstaticfilecache_cache', 'starttime', 'endtime', 'hidden', 'deleted');
$additionalColumns = array();
if(count($GLOBALS['view_data']['allDatabaseEntrysForTablePages']) > 0) {
	$additionalColumns = array_diff($GLOBALS['view_data']['allDatabaseEntrysForTablePages'][0]->getRecordKeys(), $defaultColumns);
	sort( $additionalColumns );
}
?>

<style type="text/css">
<!--
	table td.nowrap		{ white-space:nowrap; }
	table.lrPadding td	{ padding-left: 5px; padding-right: 5px; }
-->
</style>


<h2>Alle Datenbankeinträge (<?php echo count($GLOBALS['view_data']['allDatabaseEntrysForTablePages']). ' Einträge';?>):</h2>
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
	foreach($GLOBALS['view_data']['allDatabaseEntrysForTablePages'] as $databaseEntry) {
		$cleanerStrategies = explode(',', $databaseEntry->getTx_extracache_cleanerstrategies());
		$cacheEvents = explode(',', $databaseEntry->getTx_extracache_events());
		$starttime = $databaseEntry->getStarttime() > 0 ? date('d.m.Y H:i:s',$databaseEntry->getStarttime()) : '-----';
		$endtime = $databaseEntry->getEndtime() > 0 ? date('d.m.Y H:i:s',$databaseEntry->getEndtime()) : '-----';
		?>
		<tr class="bgColor4">
			<td class="nowrap"><?php echo $databaseEntry->getUid(); ?></td>
			<td class="nowrap"><?php echo $databaseEntry->getPid(); ?></td>
			<td class="nowrap"><?php echo $databaseEntry->getTitle(); ?></td>
			<td class="nowrap">
				<ol>
					<?php
					foreach($cleanerStrategies as $cleanerStrategy) {
						echo '<li>'.$cleanerStrategy.'</li>';
					}
					?>
				</ol>
			</td>
			<td class="nowrap">
				<ul>
					<?php
					foreach($cacheEvents as $cacheEvent) {
						echo '<li>'.$cacheEvent.'</li>';
					}
					?>
				</ul>
			</td>
			<td class="nowrap"><?php echo $databaseEntry->getTx_ncstaticfilecache_cache(); ?></td>
			<td class="nowrap"><?php echo $starttime; ?></td>
			<td class="nowrap"><?php echo $endtime; ?></td>
			<td class="nowrap"><?php echo $databaseEntry->getHidden(); ?></td>
			<td class="nowrap"><?php echo $databaseEntry->getDeleted(); ?></td>
			<?php
			foreach ($additionalColumns as $additionalColumn) {
				$methodName = 'get'.ucfirst($additionalColumn);
				$value = call_user_func(array($databaseEntry, $methodName));
				if(in_array($additionalColumn, array('SYS_LASTCHANGED','crdate','tstamp'))) {
					$value = date('d.m.Y H:i:s', $value);
				}
				echo '<td class="nowrap">'.$value.'</td>';
			}
			?>
		</tr>
		<?php 
	}
	?>
</table>