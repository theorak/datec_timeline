#
# Table structure for table 'tx_datectimeline_domain_model_date'
#
CREATE TABLE tx_datectimeline_domain_model_date (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	
	title varchar(255) DEFAULT '' NOT NULL,
	description text NOT NULL,
	participants int(11) unsigned DEFAULT '0' NOT NULL,
	start int(11) unsigned DEFAULT '0' NOT NULL,
	stop int(11) unsigned DEFAULT '0' NOT NULL,
	reminder_start int(11) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)	
);
#
# Table structure for table 'tx_datectimeline_domain_model_date_fe_users_mm'
#
CREATE TABLE tx_datectimeline_domain_model_date_fe_users_mm (
	uid int(11) NOT NULL auto_increment,
	uid_local int(11) DEFAULT '0' NOT NULL,
	uid_foreign int(11) DEFAULT '0' NOT NULL,
	tablenames varchar(30) DEFAULT '' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,
	sorting_foreign int(11) DEFAULT '0' NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign),
	PRIMARY KEY (uid)
);