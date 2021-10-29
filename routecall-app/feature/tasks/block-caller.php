<?php

NAMESPACE RouteCallApp\Tasks;

use Twilio\TwiML\VoiceResponse;
use RouteCallApp as App;

class Task_Block_Caller extends Task {
	
	  public function output( $attr = [], $r = null ){
		  $r = $this->setup_response( $r );
		  $notify_user = (bool) (get_field('block_caller_notify', 'option'));
		  if( $notify_user ){
			  $r->say( get_field('block_caller_notification', 'option'), ['voice' => App\Utils::get_api_voice(), 'language' => 'en-US']);
			  $r->hangup();
		  } else {
			  $r->reject();
		  }
		  return $r;
	  }
}