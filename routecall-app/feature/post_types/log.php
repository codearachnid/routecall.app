<?php

NAMESPACE RouteCallApp\CPT;

use RouteCallApp as App;

class Log extends Custom_Post_Type {
	
	protected $registered_object = null;
	protected $name = 'routecall_log';
	protected $args = [];
	
	public function __construct(){
		parent::__construct();
	}
	public function register(){
		$this->define_args();
		$this->registered_object = register_post_type( $this->name, $this->args );
	}
	public function define_args(){
		$this->args = wp_parse_args([
			'supports'=>['title','custom-fields'],
			'menu_position' =>100,
			'labels'=> [
				'name'                  => __( 'Logs',                   'RouteCallApp' ),
				'singular_name'         => __( 'Log',                    'RouteCallApp' ),
				'menu_name'             => __( 'Logs',                   'RouteCallApp' ),
				'name_admin_bar'        => __( 'Logs',                   'RouteCallApp' ),
				'add_new'               => __( 'Add New',                 'RouteCallApp' ),
				'add_new_item'          => __( 'Add New Log',            'RouteCallApp' ),
				'edit_item'             => __( 'Edit Log',               'RouteCallApp' ),
				'new_item'              => __( 'New Log',                'RouteCallApp' ),
				'view_item'             => __( 'View Log',               'RouteCallApp' ),
				'search_items'          => __( 'Search Logs',            'RouteCallApp' ),
				'not_found'             => __( 'No logs found',          'RouteCallApp' ),
				'not_found_in_trash'    => __( 'No logs found in trash', 'RouteCallApp' ),
				'all_items'             => __( 'All Logs',               'RouteCallApp' ),
				'featured_image'        => __( 'Featured Image',          'RouteCallApp' ),
				'set_featured_image'    => __( 'Set featured image',      'RouteCallApp' ),
				'remove_featured_image' => __( 'Remove featured image',   'RouteCallApp' ),
				'use_featured_image'    => __( 'Use as featred image',    'RouteCallApp' ),
				'insert_into_item'      => __( 'Insert into log',        'RouteCallApp' ),
				'uploaded_to_this_item' => __( 'Uploaded to this log',   'RouteCallApp' ),
				'views'                 => __( 'Filter logs list',       'RouteCallApp' ),
				'pagination'            => __( 'Log list navigation',   'RouteCallApp' ),
				'list'                  => __( 'Log list',              'RouteCallApp' ),
	
				// Labels for hierarchical post types only.
				'parent_item'        => __( 'Parent Item',                'RouteCallApp' ),
				'parent_item_colon'  => __( 'Parent Item:',               'RouteCallApp' ),
			]
		], $this->get_default_args());
	}
}