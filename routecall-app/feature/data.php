<?php

NAMESPACE RouteCallApp;

class Data {

	const CHANNEL_POST_TYPE = 'routecall_channel';
	const ROUTE_POST_TYPE = 'routecall_route';
  const BLOCKED_POST_TYPE = 'routecall_blocked';
  const LOG_POST_TYPE = 'routecall_log';
	

  public function __construct(){
    add_filter('posts_where', array($this,'correct_like_wildcards'));
  }
  
  public function update_recording_log( $data ){
    if( empty($data['CallSid']) )
      return false;
      
    $log_id = $this->get_log_by_sid( $data['CallSid'] );
    update_post_meta($log_id, 'call_recording', $data['RecordingUrl']);
    update_post_meta($log_id, 'call_recording_duration', $data['RecordingDuration']);
    update_post_meta($log_id, 'call_recording_sid', $data['RecordingSid']);
    return true;
  }
  
  public function get_log_by_sid( $call_sid = null ){
    global $wpdb;
    if( is_null($call_sid) ){
      $call_sid = $this->call_sid;
    }
    return (int) $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE 1=1 AND post_title = %s AND post_type = %s", $call_sid, self::LOG_POST_TYPE ) );
  }

  public function get_channel_by_inbound( $inbound = null ){
    $lookup_channel = new \WP_Query( [
      'post_type' => self::CHANNEL_POST_TYPE,
      'meta_query'	=> array(
        'relation'		=> 'AND',
        array(
          'key'		=> 'inbound_$_number',
          'value'		=> $inbound,
          'compare'	=> 'LIKE',
        )
      ),
      'orderby' => 'modified',
      'post_status' => ['published'],
      'posts_per_page' => 1,
    ]);
  
    return $lookup_channel->post;
  }
  
  public function get_channel_route( $channel_id = null ){
    if( !is_numeric($channel_id) && !empty($channel_id->ID) ){
      $channel_id = $channel_id->ID;
    }
	  return get_field( 'route', $channel_id );
  }
  
  public function is_caller_blocked( $from = null ){
    $from = trim(intval($from));
    if( $from == null ){
      return true;
    }
    $lookup_blocked_callers = new \WP_Query( [
      'post_type' => self::BLOCKED_POST_TYPE,
      'name' => $from,
      'meta_query'	=> array(
        'relation'		=> 'AND',
        array(
          'key'		=> 'block_call_cooldown',
          'value'		=> date('Y-m-d H:i:s'),
          'compare'	=> '>=',
          'type' => 'DATE'
        )
      ),
      'post_status' => ['published'],
      'posts_per_page' => -1,
    ]);
    
    return ($lookup_blocked_callers->found_posts > 0);
  }
  
  function correct_like_wildcards( $where ) {
    return str_replace("meta_key = 'inbound_\$_number'", "meta_key LIKE 'inbound_%_number'", $where);
  }
}