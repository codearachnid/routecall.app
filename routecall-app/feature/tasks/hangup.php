<?php

NAMESPACE RouteCallApp\Tasks;

use Twilio\TwiML\VoiceResponse;

class Task_Hangup extends Task {
	
	  public function output( $attr = null, $r = null ){
		  $r->hangup();
		return $r;
	  }
}