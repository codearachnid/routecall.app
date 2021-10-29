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
		$prompt = $attr['do_say'];
		// $gather->play($prompt);
		$gather->say($prompt);
		return $gather;
	}
	
}