<?php

NAMESPACE RouteCallApp\Tasks;

use Twilio\TwiML\VoiceResponse;

class Task_Gather_Input_Menu extends Task {
	public function output( $attr = null, $r = null ){
		// print_r($attr);
		$gather = $r->gather([
			'input' => 'dtmf',
			'action' => get_permalink($attr['action_menu_route']),
		    'numDigits' => '1',
		]);
		$prompt = $attr['do_say'];
		// $gather->play($prompt);
		$gather->say($prompt);
		return $gather;
	}
	
}