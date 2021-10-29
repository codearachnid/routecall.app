<?php

NAMESPACE RouteCallApp\Tasks;

use Twilio\TwiML\VoiceResponse;

class Task_Play_Audio extends Task {
	
	  public function output( $attr = [], $r = null ){
		  // print_r($attr);
		  $r->play( $attr['do_play']['url'], ['loop' => 1] ); // $attr['url']
		return $r;
	  }
}