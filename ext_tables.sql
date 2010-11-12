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