<h2><?php echo $GLOBALS['LANG']->getLL('headline_databaseentries');?></h2>
<ul>
	<li><a href="?action=allDatabaseEntrysForTableStaticCache"><?php echo $GLOBALS['LANG']->getLL('showAll').' ('.$GLOBALS['LANG']->getLL('table').' \''.$GLOBALS['view_data']['tableStaticCache'].'\' - '.$GLOBALS['LANG']->getLL('count').': '.$GLOBALS['view_data']['countDatbaseEntrysForTableStaticCache'].')'; ?></a></li>
	<li><a href="?action=allDatabaseEntrysForTablePages"><?php echo $GLOBALS['LANG']->getLL('showAll').' ('.$GLOBALS['LANG']->getLL('table').' \''.$GLOBALS['view_data']['tablePages'].'\' - '.$GLOBALS['LANG']->getLL('count').': '.$GLOBALS['view_data']['countDatbaseEntrysForTablePages'].')'; ?></a></li>
	<li><a href="?action=allDatabaseEntrysForTableEventqueue"><?php echo $GLOBALS['LANG']->getLL('showAll').' ('.$GLOBALS['LANG']->getLL('table').' \''.$GLOBALS['view_data']['tableEventQueue'].'\' - '.$GLOBALS['LANG']->getLL('count').': '.$GLOBALS['view_data']['countDatbaseEntrysForTableEventqueue'].')'; ?></a></li>
</ul>


<h2><?php echo $GLOBALS['LANG']->getLL('headline_cachefiles');?></h2>
<p><?php echo $GLOBALS['LANG']->getLL('count').': '.$GLOBALS['view_data']['countFiles']; ?></p>
<ul>
	<li><a href="?action=allFiles"><?php echo $GLOBALS['LANG']->getLL('showAllFiles');?></a></li>
	<li><a href="?action=allFolders"><?php echo $GLOBALS['LANG']->getLL('showAllDirectories');?></a></li>
</ul>