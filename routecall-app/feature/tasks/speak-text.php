<?php

NAMESPACE RouteCallApp\Tasks;
use RouteCallApp as App;

use Twilio\TwiML\VoiceResponse;

class Task_Speak_Text extends Task {
	
	  public function output( $attr = null, $r = null ){
		  $r->say( $attr['do_say'], ['voice' => App\Utils::get_instance()->get_api_voice(), 'language' => 'en-US']);
		return $r;
	  }
}