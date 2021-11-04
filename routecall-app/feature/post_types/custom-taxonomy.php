<?php

NAMESPACE RouteCallApp\CPT;

use RouteCallApp as App;

class Custom_Taxonomy {
	
	protected $args = [];
	   
	public function __construct(){
		add_action( 'init', [ $this, 'register' ], 20 );
	}
	
	public function register(){}
	
	public function get_default_args(){
		return $this->args;
	}
	
}

