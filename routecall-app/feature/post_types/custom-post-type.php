<?php

NAMESPACE RouteCallApp\CPT;

use RouteCallApp as App;

class Custom_Post_Type {
	public function __construct(){
		// echo '.......................'. __CLASS__;
		add_action( 'init', [ __CLASS__, 'register' ] );
	}
	
	public function register(){}
}