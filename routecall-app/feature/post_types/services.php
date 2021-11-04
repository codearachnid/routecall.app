<?php

NAMESPACE RouteCallApp\CPT;

use RouteCallApp as App;

class Services extends Custom_Taxonomy {
	
	protected $name = 'api_call_target_services';
	protected $args = [];
	
	public function __construct(){
		parent::__construct();
		add_filter( 'parent_file', [$this,'set_current_menu'] );
	}
	public function register(){
		$this->define_args();
		register_taxonomy( $this->name, ['routecall_target'], $this->args );
		add_submenu_page(
			'RouteCallApp',
			'Services',
			'Services',
			'manage_options',
			'edit-tags.php?taxonomy=' . $this->name,
			null,
			1000
		);
	}
	
	public function set_current_menu( $parent_file ){
		global $current_screen;
		if ( $current_screen->taxonomy == $this->name ) {
			$parent_file = 'RouteCallApp';
		}
		return $parent_file;
	}
	public function define_args(){
		$this->args = wp_parse_args([
			'label' => 'Services',
			'singular_label' => 'Service',
			'hierarchical' => false,
		], $this->get_default_args());
	}
	
}