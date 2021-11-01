<?php

NAMESPACE RouteCallApp\Tasks;

use Twilio\TwiML\VoiceResponse;

class Task_Gather_Input_Menu extends Task {
	public function output( $attr = null, $r = null ){
		$gather = $r->gather([
			'input' => 'dtmf',
			'action' => get_permalink($attr['action_menu_route']),
			// 'finishOnKey' => '#',
			'numDigits' => 1
		]);
		$do_say = $attr['do_say'];
		$do_play = $attr['do_play'];
		if( !empty($do_play) ){
			$gather->play($do_play['url']);
		} else {
			$gather->say($do_say);	
		}
		
		return $gather;
	}
	
}