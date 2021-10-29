<?php

NAMESPACE RouteCallApp\Tasks;

use RouteCallApp as App;
use Twilio\TwiML\VoiceResponse;

class Task_Gather_Voicemail extends Task {
	
	  public function output( $attr = null, $r = null ){
		$r->record([
			'action' => get_permalink(App\Utils::get_api_setting('call_api_callback_page')),
			'timeout' => 2, 
			'transcribe' => boolval($attr['voicemail_transcribe']),
			'playBeep' => boolval($attr['voicemail_playBeep']),
			'maxLength' => $attr['voicemail_maxLength'],
			'finishOnKey' => $attr['voicemail_finishOnKey'],
			'recordingStatusCallback' => get_permalink(App\Utils::get_api_setting('call_api_callback_page')), // update with more precision 
			'recordingStatusCallbackEvent' => 'completed',
		]);
		return $r;
	  }
}