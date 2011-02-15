#
# Table structure for table 'pages'
#
CREATE TABLE pages (
    tx_extracache_cleanerstrategies text NOT NULL,
    tx_extracache_events text NOT NULL,
) ENGINE=InnoDB;

#
# Table structure for table 'tx_ncstaticfilecache_file'
#
CREATE TABLE tx_ncstaticfilecache_file (
	tx_extracache_grouplist tinytext NOT NULL
);

#
# Table structure for table 'cache_pages'
#
CREATE TABLE cache_pages (
	tx_extracache_grouplist varchar(255) DEFAULT '0,-1' NOT NULL
);

#
# Table structure for table 'tx_extracache_fileremovequeue'
#
CREATE TABLE tx_extracache_fileremovequeue (
	id int(11) NOT NULL auto_increment,
	tstamp int(11) DEFAULT '0' NOT NULL,
	files text NOT NULL,

	PRIMARY KEY (`id`)
) ENGINE=InnoDB;

#
# Table structure for table 'tx_extracache_eventlog'
#
CREATE TABLE tx_extracache_eventlog (
	id int(11) NOT NULL auto_increment,
	event_key varchar(255) NOT NULL default '',
	start_time int(11) DEFAULT '0' NOT NULL,
	stop_time int(11) DEFAULT '0' NOT NULL,
	infos text NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;

#
# Table structure for table 'tx_extracache_eventqueue'
#
CREATE TABLE tx_extracache_eventqueue (
	id int(11) NOT NULL auto_increment,
	event_key varchar(255) NOT NULL default '',
	event_interval int(11) DEFAULT '0' NOT NULL,
	first_called_time int(11) DEFAULT '0' NOT NULL,
	status int(1) DEFAULT '0' NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;

#
# Table structure for table 'sys_log'
# enlarge db-field (because error-messages of this extension can be bigger than 255 chars)
#
CREATE TABLE sys_log (
    details text NOT NULL,
) ENGINE=InnoDB;