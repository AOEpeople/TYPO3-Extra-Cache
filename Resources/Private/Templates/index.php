<h2>Datenbank Eintr&auml;ge</h2>
<ul>
	<li><a href="?action=allDatabaseEntrysForTableStaticCache">alle anzeigen (Tabelle '<?php echo $GLOBALS['view_data']['tableStaticCache']; ?>' - Anzahl: <?php echo $GLOBALS['view_data']['countDatbaseEntrysForTableStaticCache']; ?>)</a></li>
	<li><a href="?action=allDatabaseEntrysForTablePages">alle anzeigen (Tabelle '<?php echo $GLOBALS['view_data']['tablePages']; ?>' - Anzahl: <?php echo $GLOBALS['view_data']['countDatbaseEntrysForTablePages']; ?>)</a></li>
	<li><a href="?action=allDatabaseEntrysForTableEventqueue">alle anzeigen (Tabelle '<?php echo $GLOBALS['view_data']['tableEventQueue']; ?>' - Anzahl: <?php echo $GLOBALS['view_data']['countDatbaseEntrysForTableEventqueue']; ?>)</a></li>
</ul>


<h2>Cache Dateien</h2>
<p>Anzahl: <?php echo $GLOBALS['view_data']['countFiles']; ?></p>
<ul>
	<li><a href="?action=allFiles">alle Dateien anzeigen</a></li>
	<li><a href="?action=allFolders">alle Ordner anzeigen</a></li>
</ul>