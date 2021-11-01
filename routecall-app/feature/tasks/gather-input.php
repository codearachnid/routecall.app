<?php

NAMESPACE RouteCallApp\Tasks;

use Twilio\TwiML\VoiceResponse;

class Task_Gather_Input extends Task {
	public function output( $attr = null, $r = null ){
		$gather = $r->gather([
			'input' => 'dtmf',
			'action' => get_permalink($attr['action']),
			'finishOnKey' => $attr['finishOnKey'],
			'numDigits' => intVal($attr['gather_number_of_digits'])
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