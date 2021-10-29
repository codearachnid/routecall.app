<?php

NAMESPACE RouteCallApp\Tasks;
use RouteCallApp as App;

use Twilio\TwiML\VoiceResponse;

class Task_Action_Menu extends Task {
	public function output( $attr = null, $r = null ){
		$Digits = App\Utils::get_header_param('Digits');
		// print_r($menu_key);
		// print_r($attr);
		$menu_key = 'keypress_'.$Digits;
		if( array_key_exists($menu_key, $attr)){
			$r->redirect( get_permalink($attr[$menu_key]), ['method' => 'POST']);
		} else {
			// TODO when this fails
		}

		return $r;
	}
	
}