<?php

NAMESPACE RouteCallApp\Tasks;
use RouteCallApp as App;

use Twilio\TwiML\VoiceResponse;

class Task_Action_Menu extends Task {
	public function output( $attr = null, $r = null ){
		$Digits = App\Utils::get_header_param('Digits');
		$menu_key_route = sprintf( 'keypress_%d_route', $Digits );
		if( array_key_exists($menu_key_route, $attr)){
			$do_say = sprintf( 'keypress_%d_do_say', $Digits );
			if( !empty($attr[$do_say]) ){
				$r->say($attr[$do_say]);
			}
			$do_play = sprintf( 'keypress_%d_do_play', $Digits );
			if( !empty($attr[$do_play]) ){
				$r->play($attr[$do_play]['url']);
			}
			$r->redirect( get_permalink($attr[$menu_key_route]), ['method' => 'POST']);
		} else {
			// TODO when this fails
		}

		return $r;
	}
	
}