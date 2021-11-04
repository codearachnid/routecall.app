<?php

NAMESPACE RouteCallApp\CPT;

use RouteCallApp as App;

class Target extends Custom_Post_Type {
	
	protected $registered_object = null;
	protected $name = 'routecall_target';
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
			'supports'=>['title'],
			'menu_position' =>10,
			'capability_type'     => 'page',
			'labels'=> [
				'name'                  => __( 'Targets',                   'RouteCallApp' ),
				'singular_name'         => __( 'Target',                    'RouteCallApp' ),
				'menu_name'             => __( 'Targets',                   'RouteCallApp' ),
				'name_admin_bar'        => __( 'Targets',                   'RouteCallApp' ),
				'add_new'               => __( 'Add New',                 'RouteCallApp' ),
				'add_new_item'          => __( 'Add New Target',            'RouteCallApp' ),
				'edit_item'             => __( 'Edit Target',               'RouteCallApp' ),
				'new_item'              => __( 'New Target',                'RouteCallApp' ),
				'view_item'             => __( 'View Target',               'RouteCallApp' ),
				'search_items'          => __( 'Search Targets',            'RouteCallApp' ),
				'not_found'             => __( 'No targets found',          'RouteCallApp' ),
				'not_found_in_trash'    => __( 'No targets found in trash', 'RouteCallApp' ),
				'all_items'             => __( 'All Targets',               'RouteCallApp' ),
				'featured_image'        => __( 'Featured Image',          'RouteCallApp' ),
				'set_featured_image'    => __( 'Set featured image',      'RouteCallApp' ),
				'remove_featured_image' => __( 'Remove featured image',   'RouteCallApp' ),
				'use_featured_image'    => __( 'Use as featred image',    'RouteCallApp' ),
				'insert_into_item'      => __( 'Insert into Target',        'RouteCallApp' ),
				'uploaded_to_this_item' => __( 'Uploaded to this Target',   'RouteCallApp' ),
				'views'                 => __( 'Filter targets list',       'RouteCallApp' ),
				'pagination'            => __( 'Target list navigation',   'RouteCallApp' ),
				'list'                  => __( 'Target list',              'RouteCallApp' ),
	
				// Labels for hierarchical post types only.
				'parent_item'        => __( 'Parent Item',                'RouteCallApp' ),
				'parent_item_colon'  => __( 'Parent Item:',               'RouteCallApp' ),
			]
		], $this->get_default_args());
	}
}