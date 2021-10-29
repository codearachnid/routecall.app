<?php

NAMESPACE RouteCallApp\Tasks;
use RouteCallApp as App;

use Twilio\TwiML\VoiceResponse;

class Task_Target_Dial extends Task {
	
	  public function output( $attr = null, $r = null ){

		  $target_id = $attr['dial_target']->ID;
		  $target_destination = get_field('target_destination', $target_id);
		  $dial_settings = ['action'=>get_permalink(App\Utils::get_api_setting('call_api_callback_page'))];
		  if( get_field('call_recording', $target_id) ){
			$dial_settings = array_merge($dial_settings,[
				  'record' => 'record-from-answer', 
				  'recordingStatusCallback' => get_permalink(App\Utils::get_api_setting('call_api_callback_page')), 
				  'recordingStatusCallbackEvent' => 'completed',
			]);
				  
		  }
		  
		  $r->dial( $target_destination, $dial_settings );
		  // associate the tracked logs for the call to the outbound dial of the identified target
		  $log_by_sid = App\Framework::get_instance()->track()->get_log_by_sid();
		  add_post_meta($log_by_sid, 'log_target', $target_id);


		return $r;
	  }
}