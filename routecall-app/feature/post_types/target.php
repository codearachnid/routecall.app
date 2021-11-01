<?php

NAMESPACE RouteCallApp\CPT;

use RouteCallApp as App;

class Target extends Custom_Post_Type {
	public function __construct(){
		// echo '.......................'. __CLASS__;
		add_action( 'init', [ __CLASS__, 'register' ] );
	}
	public function register(){
		// echo '.......................'. __CLASS__;
		register_post_type('wporg_product',
			array(
				'labels'      => array(
					'name'          => __('Products', 'textdomain'),
					'singular_name' => __('Product', 'textdomain'),
				),
					'public'      => true,
					'has_archive' => true,
			)
		);
	}
}