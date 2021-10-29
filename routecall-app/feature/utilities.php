<?php

NAMESPACE RouteCallApp;

class Utils{
  private static $instance;
  public static function get_header_param($key = null){
	$safe_headers = ['Called','ToState','CallerCountry','Direction','Digits','CallerState','ToZip','CallSid','To','CallerName','CallerZip','ToCountry','ApiVersion','CalledZip','CallStatus','CalledCity','From','AccountSid','CalledCountry','ApplicationSid','CallerCity','Caller','FromCountry','ToCity','FromCity','CalledState','FromZip','FromState'];
	$value = null;
	if ( $key == 'ALL' ) {
	  // safely whitelist all approved keys of the global request
	  $value = array_intersect_key($_REQUEST,array_flip($safe_headers));
	} else if ( !empty($_REQUEST[$key]) && in_array($key, $safe_headers) ){
	  // safely return global request property
	  $value = sanitize_text_field( $_REQUEST[$key] );
	} else {
	  $value = null;
	}
	return $value;
  }
  
  public static function get_safe_phone( $phone_number ){
	  $phone_number = (int) filter_var($phone_number, FILTER_SANITIZE_NUMBER_INT);
	  if( substr($phone_number, 0, 1) == "1"){
		  $phone_number = "+" . $phone_number;
	  } else if( substr($phone_number, 0, 1) != "+"){
		  if( substr($phone_number, 1, 1) != "1"){
			  $phone_number = "+1" . $phone_number;
		  } else {
			  $phone_number = "+" . $phone_number;
		  }
	  }
	  return $phone_number;
  }
  
  public static function get_api_setting( $key = null ){
	  if( is_null($key) )
	  	return $key;
		  
	return get_field( $key,'option');
  }
  
  public static function  get_api_voice(){
	  $voice = self::get_api_setting('call_api_voice_language'); //get_field('call_api_voice_language','option');
	  return !empty($voice) ? $voice->name : 'Polly.Ivy';
  }

  /**
	 * Gets an instance of our plugin.
	 *
	 * @return WP_Kickass_Plugin
	 */
	public static function get_instance()
	{
		if (null === self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
