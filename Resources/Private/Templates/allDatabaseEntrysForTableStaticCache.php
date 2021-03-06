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


<!-- show from for db-record-search -->
<h2><?php echo $GLOBALS['LANG']->getLL('headline_filter');?>:</h2>
<form name="setConfigSearchPhraseForTableStaticCache" action="index.php" method="GET">
	<input type="hidden" name="action" value="setConfigSearchPhraseForTableStaticCache" />
	<input type="text" name="searchPhraseForTableStaticCache" value="<?php echo $GLOBALS['BE_USER']->getModuleData('tx_extracache_manager_searchPhraseForTableStaticCache'); ?>" />
	<input onClick="javascript:this.form.submit();" type="button" value="<?php echo $GLOBALS['LANG']->getLL('startDbRecordSearch');?>" />
</form>
<br /><?php echo $GLOBALS['LANG']->getLL('startFilterBySearchPhraseForDbRecordsInfo');?>
<br /><br /><br />


<h2><?php echo $GLOBALS['LANG']->getLL('headline_allDatabaseentries').' ('.count($GLOBALS['view_data']['allDatabaseEntrysForTableStaticCache']). ' '.$GLOBALS['LANG']->getLL('entries').'):';?></h2>
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
		$timeout = ($databaseEntry->getTstamp() > 0) ? \TYPO3\CMS\Backend\Utility\BackendUtility::calcAge(($databaseEntry->getCache_timeout()),$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:labels.minutesHoursDaysYears')) : '';
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