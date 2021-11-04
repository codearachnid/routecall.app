<?php

NAMESPACE RouteCallApp\CPT;

use RouteCallApp as App;

class Route extends Custom_Post_Type {
	
	protected $registered_object = null;
	protected $name = 'routecall_route';
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
			'supports'=>['title','page-attributes'],
			'capability_type'     => 'page',
			'menu_position' =>100,
			'labels'=> [
				'name'                  => __( 'Routes',                   'RouteCallApp' ),
				'singular_name'         => __( 'Route',                    'RouteCallApp' ),
				'menu_name'             => __( 'Routes',                   'RouteCallApp' ),
				'name_admin_bar'        => __( 'Routes',                   'RouteCallApp' ),
				'add_new'               => __( 'Add New',                 'RouteCallApp' ),
				'add_new_item'          => __( 'Add New Route',            'RouteCallApp' ),
				'edit_item'             => __( 'Edit Route',               'RouteCallApp' ),
				'new_item'              => __( 'New Route',                'RouteCallApp' ),
				'view_item'             => __( 'View Route',               'RouteCallApp' ),
				'search_items'          => __( 'Search Routes',            'RouteCallApp' ),
				'not_found'             => __( 'No routes found',          'RouteCallApp' ),
				'not_found_in_trash'    => __( 'No routes found in trash', 'RouteCallApp' ),
				'all_items'             => __( 'All Routes',               'RouteCallApp' ),
				'featured_image'        => __( 'Featured Image',          'RouteCallApp' ),
				'set_featured_image'    => __( 'Set featured image',      'RouteCallApp' ),
				'remove_featured_image' => __( 'Remove featured image',   'RouteCallApp' ),
				'use_featured_image'    => __( 'Use as featred image',    'RouteCallApp' ),
				'insert_into_item'      => __( 'Insert into route',        'RouteCallApp' ),
				'uploaded_to_this_item' => __( 'Uploaded to this route',   'RouteCallApp' ),
				'views'                 => __( 'Filter routes list',       'RouteCallApp' ),
				'pagination'            => __( 'Route list navigation',   'RouteCallApp' ),
				'list'                  => __( 'Route list',              'RouteCallApp' ),
	
				// Labels for hierarchical post types only.
				'parent_item'        => __( 'Parent Item',                'RouteCallApp' ),
				'parent_item_colon'  => __( 'Parent Item:',               'RouteCallApp' ),
			]
		], $this->get_default_args());
	}
	
}