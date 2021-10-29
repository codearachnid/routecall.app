<?php

NAMESPACE RouteCallApp\Tasks;

use Twilio\TwiML\VoiceResponse;

class Task_Gather_Voicemail extends Task {
	
	  public function output( $attr = null, $r = null ){
		// print_r($attr);
		//timeout=2
		return $r;
	  }
}