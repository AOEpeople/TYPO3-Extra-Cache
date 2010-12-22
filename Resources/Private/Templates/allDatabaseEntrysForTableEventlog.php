<?php
$defaultColumns = array('event_key','start_time', 'stop_time','infos');
$additionalColumns = array();
if(count($GLOBALS['view_data']['allDatabaseEntrysForTableEventlog']) > 0) {
	$additionalColumns = array_diff($GLOBALS['view_data']['allDatabaseEntrysForTableEventlog'][0]->getRecordKeys(), $defaultColumns);
	sort( $additionalColumns );
}
?>

<style type="text/css">
<!--
	table td.nowrap		{ white-space:nowrap; }
	table.lrPadding td	{ padding-left: 5px; padding-right: 5px; }
-->
</style>


<!-- show from for files-search -->
<form name="setConfigSearchPhraseForFiles" action="index.php" method="GET">
	<input type="hidden" name="action" value="setConfigDateFilterForDbRecords" />
	<input type="hidden" name="routeToAction" value="allDatabaseEntrysForTableEventlogAction" />
	<?php echo $GLOBALS['LANG']->getLL('startFilterFromDate');?>: <input type="text" name="startDateFilterForDbRecords" value="<?php echo $GLOBALS['BE_USER']->getModuleData('tx_extracache_manager_startDateFilterForDbRecords'); ?>" />&nbsp;
	<?php echo $GLOBALS['LANG']->getLL('startFilterTillDate');?>: <input type="text" name="stopDateFilterForDbRecords" value="<?php echo $GLOBALS['BE_USER']->getModuleData('tx_extracache_manager_stopDateFilterForDbRecords'); ?>" />
	<input onClick="javascript:this.form.submit();" type="button" value="<?php echo $GLOBALS['LANG']->getLL('startFilterByDate');?>" />
</form>
<br /><?php echo $GLOBALS['LANG']->getLL('startFilterByDateInfo');?>
<br /><br />


<h2><?php echo $GLOBALS['LANG']->getLL('headline_allDatabaseentries').' ('.count($GLOBALS['view_data']['allDatabaseEntrysForTableEventlog']). ' '.$GLOBALS['LANG']->getLL('entries').'):';?></h2>
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
	foreach($GLOBALS['view_data']['allDatabaseEntrysForTableEventlog'] as $databaseEntry) {
		$infos = '';
		$infoObjects = unserialize( $databaseEntry->getInfos() );
		if(count($infoObjects) > 0) {
			$infos .= '<ul>';
		}
		foreach($infoObjects as $infoObject) {
			$infoText = date('d.m.Y H:i:s',$infoObject->getTimestamp()).' - '.$infoObject->getTitle();
			if($infoObject->getType() === Tx_Extracache_Domain_Model_Info::TYPE_exception) {
				$infoText = '<strong>'.$infoText.'</strong>';
			}
			$infos .= '<li>'.$infoText.'</li>';
		}
		if(count($infoObjects) > 0) {
			$infos .= '</ul>';
		}
		?>
		<tr class="bgColor4">
			<td class="nowrap"><?php echo $databaseEntry->getEvent_key(); ?></td>
			<td class="nowrap"><?php echo date('d.m.Y H:i:s',$databaseEntry->getStart_time()); ?></td>
			<td class="nowrap"><?php echo date('d.m.Y H:i:s',$databaseEntry->getStop_time()); ?></td>
			<td class="nowrap"><?php echo $infos; ?></td>
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