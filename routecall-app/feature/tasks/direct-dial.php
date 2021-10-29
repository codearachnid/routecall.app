<?php

NAMESPACE RouteCallApp\Tasks;

use Twilio\TwiML\VoiceResponse;

class Task_Direct_Dial extends Task {
	
	  public function output( $attr = null, $r = null ){
		$r->dial( $attr['do_dial'] );
		return $r;
	  }
}