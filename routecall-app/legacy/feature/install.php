<?php

class RouteCallApp_Install {
  protected $tables = [

    'routecallapp_event' => [
      'exists' => false,
      'create_sql' => 'CREATE TABLE `%s` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `date` datetime DEFAULT NULL,
        `status` varchar(20) DEFAULT NULL,
        `type` varchar(20) DEFAULT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1;' ],

    'routecallapp_action' => [
      'exists' => false,
      'create_sql' => 'CREATE TABLE `%s` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `event_id` bigint(20) unsigned NOT NULL DEFAULT 0,
        `route_id` bigint(20) unsigned NOT NULL DEFAULT 0,
        `action` varchar(255) DEFAULT NULL,
        `status` varchar(11) DEFAULT NULL,
        `reference` varchar(100) DEFAULT NULL,
        `data` longtext DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `post_id` (`event_id`)
        KEY `action` (`action`(191))
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1;' ],

    'routecallapp_lookup' => [
      'exists' => false,
      'create_sql' => 'CREATE TABLE `%s` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `route_group` bigint(20) DEFAULT NULL,
        `name` varchar(25) DEFAULT NULL,
        `zip` int(11) DEFAULT NULL,
        `city` varchar(60) DEFAULT NULL,
        `state` varchar(2) DEFAULT NULL,
        `country` varchar(10) DEFAULT NULL,
        `latitude` float(10,6) DEFAULT NULL,
        `longitude` float(10,6) DEFAULT NULL,
        PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=latin1;' ],

    'routecallapp_geozip' => [
      'exists' => false,
      'create_sql' => 'CREATE TABLE `%s` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `recordid` varchar(40) DEFAULT NULL,
        `city` varchar(50) DEFAULT NULL,
        `state` varchar(2) DEFAULT NULL,
        `zip` int(5) DEFAULT NULL,
        `dst` int(11) DEFAULT NULL,
        `timezone` int(11) DEFAULT NULL,
        `longitude` float(10,6) DEFAULT NULL,
        `latitude` float(10,6) DEFAULT NULL,
        PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=latin1;' ],

    'routecallapp_event_detail' => [
      'exists' => false,
      'create_sql' => 'CREATE TABLE `%s` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `post_id` bigint(20) DEFAULT NULL,
        `date_UTC` datetime DEFAULT NULL,
        `status_code` int(11) DEFAULT NULL,
        `req_verb` varchar(4) DEFAULT NULL,
        `req_url` varchar(255) DEFAULT NULL,
        `req_direction` varchar(10) DEFAULT NULL,
        `req_callstatus` varchar(24) DEFAULT NULL,
        `req_applicationsid` varchar(34) DEFAULT NULL,
        `req_accountsid` varchar(34) DEFAULT NULL,
        `req_callsid` varchar(34) DEFAULT NULL,
        `req_from` varchar(12) DEFAULT NULL,
        `req_caller` varchar(12) DEFAULT NULL,
        `req_callername` varchar(128) DEFAULT NULL,
        `req_callercity` varchar(64) DEFAULT NULL,
        `req_fromcity` varchar(64) DEFAULT NULL,
        `req_callerstate` varchar(4) DEFAULT NULL,
        `req_fromstate` varchar(4) DEFAULT NULL,
        `req_callerzip` varchar(10) DEFAULT NULL,
        `req_fromzip` varchar(10) DEFAULT NULL,
        `req_callercountry` varchar(32) DEFAULT NULL,
        `req_fromcountry` varchar(32) DEFAULT NULL,
        `req_to` varchar(12) DEFAULT NULL,
        `req_called` varchar(12) DEFAULT NULL,
        `req_tocity` varchar(64) DEFAULT NULL,
        `req_calledcity` varchar(64) DEFAULT NULL,
        `req_tostate` varchar(4) DEFAULT NULL,
        `req_calledstate` varchar(4) DEFAULT NULL,
        `req_tozip` varchar(10) DEFAULT NULL,
        `req_calledzip` varchar(10) DEFAULT NULL,
        `req_tocountry` varchar(32) DEFAULT NULL,
        `req_calledcountry` varchar(32) DEFAULT NULL,
        `req_apiversion` varchar(32) DEFAULT NULL,
        `response_time` int(11) DEFAULT NULL,
        `response_body` longtext DEFAULT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1;' ],
  ];
  public function checkDB(){
    global $wpdb;
    foreach( $tables as $table_name => $data ){
      $table = $wpdb->prefix . $table_name;
    }
  }

  public function createTable(){
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    global $wpdb;
    foreach( $this->tables as $table_name => $data ){
      $create_sql = sprintf( $data['create_sql'], $wpdb->prefix . $table_name);
      if( maybe_create_table( $wpdb->prefix . $table_name, $create_sql ) ){
        $this->tables[ $table_name ]['exists'] = true;
      }
    }
  }
}
