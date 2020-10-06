#
# Table structure for table 'tx_timelog_domain_model_project'
#
CREATE TABLE tx_timelog_domain_model_project (

    handle varchar(255) DEFAULT '' NOT NULL,
    title varchar(255) DEFAULT '' NOT NULL,
    description text,
    internal_note text,
    active_time double(11,2) DEFAULT '0.00' NOT NULL,
    heap_time double(11,2) DEFAULT '0.00' NOT NULL,
    batch_time double(11,2) DEFAULT '0.00' NOT NULL,
    client int(11) unsigned DEFAULT '0',
    owner int(11) unsigned DEFAULT '0',
    cc_email varchar(255) DEFAULT '' NOT NULL,
    tasks int(11) unsigned DEFAULT '0' NOT NULL,
    task_groups int(11) unsigned DEFAULT '0' NOT NULL

);

#
# Table structure for table 'tx_timelog_domain_model_task'
#
CREATE TABLE tx_timelog_domain_model_task (

    handle varchar(255) DEFAULT '' NOT NULL,
    title varchar(255) DEFAULT '' NOT NULL,
    description text,
    active_time double(11,2) DEFAULT '0.00' NOT NULL,
    batch_date int(11) DEFAULT '0' NOT NULL,
    project int(11) unsigned DEFAULT '0',
    worker int(11) unsigned DEFAULT '0',
    task_group int(11) unsigned DEFAULT '0',
    intervals int(11) unsigned DEFAULT '0' NOT NULL

);

#
# Table structure for table 'tx_timelog_domain_model_taskgroup'
#
CREATE TABLE tx_timelog_domain_model_taskgroup (

    handle varchar(255) DEFAULT '' NOT NULL,
    title varchar(255) DEFAULT '' NOT NULL,
    description text,
    internal_note text,
    time_target double(11,2) DEFAULT '0.00' NOT NULL,
    time_deviation double(11,2) DEFAULT '0.00' NOT NULL,
    active_time double(11,2) DEFAULT '0.00' NOT NULL,
    heap_time double(11,2) DEFAULT '0.00' NOT NULL,
    batch_time double(11,2) DEFAULT '0.00' NOT NULL,
    project int(11) unsigned DEFAULT '0',
    tasks int(11) unsigned DEFAULT '0' NOT NULL

);

#
# Table structure for table 'tx_timelog_domain_model_interval'
#
CREATE TABLE tx_timelog_domain_model_interval (

    task int(11) unsigned DEFAULT '0' NOT NULL,
    start_time int(11) DEFAULT '0' NOT NULL,
    end_time int(11) DEFAULT '0' NOT NULL,
    duration double(11,2) DEFAULT '0.00' NOT NULL

);

#
# Table structure for table 'fe_users'
#
CREATE TABLE fe_users (

    tx_timelog_owner_email varchar(255) DEFAULT '' NOT NULL,

);
