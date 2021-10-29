<?php

NAMESPACE RouteCallApp\Tasks;

use Twilio\TwiML\VoiceResponse;

class Task_Redirect_Route extends Task {
	
	  public function output( $attr = null, $r = null ){
		  if( filter_var($attr['url'], FILTER_VALIDATE_URL) ) {
			$url = $attr['url'];  
		  } else if( is_object($attr['url']) && property_exists($attr['url'], 'ID') ){
			  $url = get_permalink($attr['url']);
		  } else {
			  $url = get_permalink($attr['url']);
		  }
		$r->redirect( $url, ['method' => 'POST']);
		return $r;
	  }
}