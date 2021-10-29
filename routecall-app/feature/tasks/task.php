<?php

NAMESPACE RouteCallApp\Tasks;

use RouteCallApp as App;
use Twilio\TwiML\VoiceResponse;

class Task {

  public function __construct(){

  }

  public function setup_response( $attr = null, $r = null ){
	if( is_null( $r ) ) {
	  $r = new VoiceResponse(); // TWILIO setup response
	}
	return $r;
  }
  
  public function output(){
	  
  }

}
