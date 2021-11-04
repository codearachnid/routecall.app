<?php

NAMESPACE RouteCallApp\CPT;

use RouteCallApp as App;

class Blocked extends Custom_Post_Type {
	
	protected $registered_object = null;
	protected $name = 'routecall_blocked';
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
			'menu_position' =>200,
			'labels'=> [
				'name'                  => __( 'Blocked Callers',                   'RouteCallApp' ),
				'singular_name'         => __( 'Blocked Caller',                    'RouteCallApp' ),
				'menu_name'             => __( 'Blocked Callers',                   'RouteCallApp' ),
				'name_admin_bar'        => __( 'Blocked Callers',                   'RouteCallApp' ),
				'add_new'               => __( 'Add New',                 'RouteCallApp' ),
				'add_new_item'          => __( 'Add New Blocked Caller',            'RouteCallApp' ),
				'edit_item'             => __( 'Edit Blocked Caller',               'RouteCallApp' ),
				'new_item'              => __( 'New Blocked Caller',                'RouteCallApp' ),
				'view_item'             => __( 'View Blocked Caller',               'RouteCallApp' ),
				'search_items'          => __( 'Search Blocked Callers',            'RouteCallApp' ),
				'not_found'             => __( 'No blocked callers found',          'RouteCallApp' ),
				'not_found_in_trash'    => __( 'No blocked callers found in trash', 'RouteCallApp' ),
				'all_items'             => __( 'All Blocked Callers',               'RouteCallApp' ),
				'featured_image'        => __( 'Featured Image',          'RouteCallApp' ),
				'set_featured_image'    => __( 'Set featured image',      'RouteCallApp' ),
				'remove_featured_image' => __( 'Remove featured image',   'RouteCallApp' ),
				'use_featured_image'    => __( 'Use as featred image',    'RouteCallApp' ),
				'insert_into_item'      => __( 'Insert into blocked caller',        'RouteCallApp' ),
				'uploaded_to_this_item' => __( 'Uploaded to this blocked caller',   'RouteCallApp' ),
				'views'                 => __( 'Filter blocked callers list',       'RouteCallApp' ),
				'pagination'            => __( 'Blocked Callers list navigation',   'RouteCallApp' ),
				'list'                  => __( 'Blocked Caller list',              'RouteCallApp' ),
	
				// Labels for hierarchical post types only.
				'parent_item'        => __( 'Parent Item',                'RouteCallApp' ),
				'parent_item_colon'  => __( 'Parent Item:',               'RouteCallApp' ),
			]
		], $this->get_default_args());
	}
	
}