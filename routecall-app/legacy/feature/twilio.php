<?php

Class RouteCallApp_Twilio {
  function load(){
    if( ! defined( RouteCallApp_NAMESPACE . '_Twilio_Loaded') ) {
      require_once RouteCallApp_DIR . 'vender/twilio-php-main/src/Twilio/autoload.php';
      define ( RouteCallApp_NAMESPACE . '_Twilio_Loaded', true );
    }
    return true;
  }
}
