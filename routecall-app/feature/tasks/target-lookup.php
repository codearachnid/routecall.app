<?php

NAMESPACE RouteCallApp\Tasks;
use RouteCallApp as App;

use Twilio\TwiML\VoiceResponse;

class Task_Target_Lookup extends Task {
	
	  public function output( $attr = null, $r = null ){

		  $digits = App\Utils::get_header_param('Digits');

		//search targets by exact match to zipcode
		  $lookup_target = new \WP_Query( [
				'post_type' => 'routecall_target',
				'meta_query'	=> array(
				  'relation'		=> 'AND',
				  array(
					'key'		=> 'target_zipcodes',
					'value'		=> $digits,
					'compare'	=> 'IN',
				  )
				),
				'orderby' => 'rand',
				'post_status' => ['published'],
				'posts_per_page' => 1,
			  ]);
			  if( !empty($digits) && !empty($lookup_target->post ) ){
				  $target_id = $lookup_target->post->ID;
				  $target_destination = get_field('target_destination', $target_id); //get_post_meta($lookup_target->post->ID, 'target_destination', true);
				  $identified_name = get_field('identified_name', $target_id);
				  $soft_intro = str_replace("{name}", $identified_name, $attr['notify_caller_before_dial'] );
				  $r->say( $soft_intro );
				  
				  
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
			  } else {
				// print_r($attr);
				// print_r($lookup_target);
				// TODO improve the reuse of redirect-route.php to address this
				// $redirect_attr =['url', ];lookup_alternative_route
				// $this->register('redirect_route')->output( $selected_task, $r );				  
				$r->redirect( get_permalink($attr['lookup_alternative_route']), ['method' => 'POST']);
			  }

		return $r;
	  }
}