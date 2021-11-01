<?php

NAMESPACE RouteCallApp;

use Twilio\Rest\Client;

class Track {	
	
	private $call_sid = null;
	const POST_TYPE = 'routecall_log';
	private $log_to_db = TRUE;

  public function __construct( $call_sid = null ){
	  $this->call_sid = $call_sid;
	  $this->log_to_db = get_field('call_api_disable_logging', 'option') ? FALSE : TRUE;
	  add_action("add_meta_boxes", array($this, "add_custom_meta_box") );
  }
  
  public function event($data, $call_sid = null){
	  global $user_ID, $wpdb;
	  
	  if( !$this->log_to_db || empty($data['action']) ){
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
		  
		  add_post_meta($log_id, 'twilio_From', Utils::get_header_param('From'));
		  add_post_meta($log_id, 'twilio_Called', Utils::get_header_param('Called'));
		  add_post_meta($log_id, 'twilio_CallerName', Utils::get_header_param('CallerName'));
		  add_post_meta($log_id, 'twilio_FromCountry', Utils::get_header_param('FromCountry'));
		  add_post_meta($log_id, 'twilio_FromCity', Utils::get_header_param('FromCity'));
		  add_post_meta($log_id, 'twilio_FromState', Utils::get_header_param('FromState'));
		  add_post_meta($log_id, 'twilio_FromZip', Utils::get_header_param('FromZip'));
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
  
  public function update_log_from_twilio( $log_id = null ){
	  
	  $call_sid = get_the_title( $log_id );
	  $account_sid = Utils::get_api_setting('twilio_account_sid');
	  $auth_token = Utils::get_api_setting('twilio_auth_token');
	  
	  $client= new Client($account_sid, $auth_token);
	  
	  $call = $client->calls($call_sid)->fetch();
	  $recordings = $client->recordings->read(array( 'callSid' => $call_sid ), 1);
	  
	  
		update_post_meta($log_id, 'twilio_From', $call->from);
		update_post_meta($log_id, 'twilio_Called', $call->to);
		update_post_meta($log_id, 'twilio_CallerName', $call->callerName);
		update_post_meta($log_id, 'call_duration', $call->duration);
		
		//https://api.twilio.com/2010-04-01/Accounts/ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/Recordings/RExxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.mp3?Download=false
		
		if(!empty($recordings[0]->sid)){
			$recording_url = sprintf('https://api.twilio.com/%s/Accounts/%s/Recordings/%s.mp3?Download=false', 
				$call->apiVersion,
				$call->accountSid,
				$recordings[0]->sid);
			update_post_meta($log_id, 'call_recording_sid', $recordings[0]->sid);
			update_post_meta($log_id, 'call_recording', $recording_url);
			update_post_meta($log_id, 'call_recording_duration', $recordings[0]->duration);
		}
		
		
		
		// DEBUG
		// echo '$recordings';
		// print_r($recordings[0]);
		// foreach( $recordings as $entry){
		// print_r($entry->sid);	
		// }
		  

  }
  
  public function add_custom_meta_box(){
	  add_meta_box("call_api_track_meta_api_response", "API Response", array($this,"meta_display_api_response"), self::POST_TYPE, "normal", "core", null);
	  // add_meta_box("call_api_track_meta_events", "Events", array($this,"meta_display_event_list"), self::POST_TYPE, "normal", "default", null);
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