<?php

NAMESPACE RouteCallApp;

class Track {	
	
	private $call_sid = null;
	const POST_TYPE = 'routecall_log';

  public function __construct( $call_sid = null ){
	  $this->call_sid = $call_sid;
	  add_action("add_meta_boxes", array($this, "add_custom_meta_box") );
  }
  
  public function event($data, $call_sid = null){
	  global $user_ID, $wpdb;
	  
	  if( empty($data['action']) ){
		  return false;
	  }
	  
	  $call_sid = !empty($call_sid) ? $call_sid : $this->call_sid;
	  
	  $log_id = $this->get_log_by_sid($call_sid);
  
	  if ( !$log_id ) {
		  $new_log = array(
			  'post_title' => $call_sid,
			  'post_content' => '',
			  'post_status' => 'publish',
			  'post_date' => date('Y-m-d H:i:s'),
			  'post_author' => '',
			  'post_type' => self::POST_TYPE
		  );
		  $log_id = wp_insert_post($new_log);
	  }
	  
	  // do meta stuff
	  add_post_meta( $log_id, $data['action'], $data['raw'], false );
	  
	  return $log_id;
  }
  
  public function api_log( $call_sid, $data ){
	  global $wpdb;
	  $log_id = $this->get_log_by_sid($call_sid);
	  $table = $wpdb->prefix . 'routecallapp_event_detail';
	  
	  $consolidated_data = [
		  'post_id' => $log_id,
		  'date_UTC' => date('Y-m-d H:i:s'),
		  'status_code' => '',
		  'req_verb' => '',
		  'req_url' => '',
		  'req_direction' => '',
		  'req_callstatus' => '',
		  'req_applicationsid' => '',
		  'req_accountsid' => '',
		  'req_callsid' => !empty($data['CallSid']) ? $data['CallSid'] : '',
		  'req_from' => '',
		  'req_caller' => '',
		  'req_callername' => '',
		  'req_callercity' => '',
		  'req_fromcity' => '',
		  'req_callerstate' => '',
		  'req_fromstate' => '',
		  'req_callerzip' => '',
		  'req_fromzip' => '',
		  'req_callercountry' => '',
		  'req_fromcountry' => '',
		  'req_to' => '',
		  'req_called' => '',
		  'req_tocity' => '',
		  'req_calledcity' => '',
		  'req_tostate' => '',
		  'req_calledstate' => '',
		  'req_tozip' => '',
		  'req_calledzip' => '',
		  'req_tocountry' => '',
		  'req_calledcountry' => '',
		  'req_apiversion' => '',
		  'response_time' => '',
		  'response_body' => ''];
	  
	  
	  $wpdb->insert($table, $consolidated_data, array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ));
	  
	  return $log_id;
  }
  
  public function set_call_sid( $call_sid ){
	  $this->call_sid = $call_sid;
  }
  
  public function get_log_by_sid( $call_sid = null ){
	  global $wpdb;
	  if( is_null($call_sid) ){
		  $call_sid = $this->call_sid;
	  }
	  return (int) $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE 1=1 AND post_title = %s AND post_type = %s", $call_sid, self::POST_TYPE ) );
  }
  
  public function generate_sid($length = 34) {
	  return substr(md5(time()), 0, $length);
  }
  
  public function add_custom_meta_box(){
	  add_meta_box("call_api_track_meta_api_response", "API Response", array($this,"meta_display_api_response"), self::POST_TYPE, "normal", "core", null);
	  add_meta_box("call_api_track_meta_events", "Events", array($this,"meta_display_event_list"), self::POST_TYPE, "normal", "default", null);
  }
  public function meta_display_api_response( $object ){
	  $api_responses = get_post_meta($object->ID, 'RouteCallAppFramework.route_template.api_respond');
		foreach($api_responses as $api_response){

				  $dom = new \DOMDocument('1.0');
				  $dom->preserveWhiteSpace = true;
				  $dom->formatOutput = true;
				  $dom->loadXML($api_response);
				  $xml_pretty = $dom->saveXML();
				  printf('<textarea rows="10" cols="150" style="border:1px solid #ccc;">%s</textarea>', $xml_pretty);

		}
  }
  public function meta_display_event_list( $object ) {
	  // global $wpdb;
	  // $table = $wpdb->prefix . 'routecallapp_event_detail';
	  // $wpdb->
	  // print_r($object);
	  $events = get_post_meta($object->ID); //get_metadata( $object->post_type, $object->ID );
	  $exclude = ['RouteCallAppFramework.route_template.api_respond','log_target','_edit_lock','_edit_last','_log_target'];
  
  	foreach($events as $event => $attr){
		  if( in_array($event, $exclude) ){
			  continue;
		  }
		  // header
		  printf("<h3>%s</h3>", $event);
		  echo "<pre>";
		  foreach($attr as $value){
			  if( is_serialized( $value ) ){
				  $data = unserialize($value);
				  print_r($data);
			  } else {
				  echo $value;
			  }
		  }
		  echo "</pre>";
			

	  }
 
  
  }
}