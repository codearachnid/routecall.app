<?php

NAMESPACE RouteCallApp\Tasks;
use RouteCallApp as App;

// use Twilio\TwiML\VoiceResponse;
// use Twilio\TwiML\MessagingResponse;
use Twilio\Rest\Client;

class Task_Send_Sms extends Task {
	
	  public function output( $attr = null, $r = null ){
		$send_to = null;
		if( $attr['sms_send_to_caller'] ){
			$send_to = App\Utils::get_header_param('From');
 		} else if( $attr['sms_send_to_custom'] ){
			$send_to = $attr['sms_send_to_custom'];
		}
		
		$send_to =  App\Utils::get_safe_phone($send_to);
		$account_sid = App\Utils::get_api_setting('twilio_account_sid');
		$auth_token = App\Utils::get_api_setting('twilio_auth_token');
		$twilio_number = App\Utils::get_api_setting('sms_message_from');
		
		$client = new Client($account_sid, $auth_token);
		$client->messages->create(
			$send_to,
			array(
				'from' => $twilio_number,
				'body' => $attr['sms_message']
			)
		);
		return $r;
	  }
}