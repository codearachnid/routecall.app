<?php

NAMESPACE RouteCallApp\CPT;

use RouteCallApp as App;

class Channel extends Custom_Post_Type {
	
	protected $registered_object = null;
	protected $name = 'routecall_channel';
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
			'capability_type'     => 'page',
			'menu_position' =>100,
			'labels'=> [
				'name'                  => __( 'Channels',                   'RouteCallApp' ),
				'singular_name'         => __( 'Channel',                    'RouteCallApp' ),
				'menu_name'             => __( 'Channels',                   'RouteCallApp' ),
				'name_admin_bar'        => __( 'Channels',                   'RouteCallApp' ),
				'add_new'               => __( 'Add New',                 'RouteCallApp' ),
				'add_new_item'          => __( 'Add New Channel',            'RouteCallApp' ),
				'edit_item'             => __( 'Edit Channel',               'RouteCallApp' ),
				'new_item'              => __( 'New Channel',                'RouteCallApp' ),
				'view_item'             => __( 'View Channel',               'RouteCallApp' ),
				'search_items'          => __( 'Search Channels',            'RouteCallApp' ),
				'not_found'             => __( 'No channels found',          'RouteCallApp' ),
				'not_found_in_trash'    => __( 'No channels found in trash', 'RouteCallApp' ),
				'all_items'             => __( 'All Channels',               'RouteCallApp' ),
				'featured_image'        => __( 'Featured Image',          'RouteCallApp' ),
				'set_featured_image'    => __( 'Set featured image',      'RouteCallApp' ),
				'remove_featured_image' => __( 'Remove featured image',   'RouteCallApp' ),
				'use_featured_image'    => __( 'Use as featred image',    'RouteCallApp' ),
				'insert_into_item'      => __( 'Insert into channel',        'RouteCallApp' ),
				'uploaded_to_this_item' => __( 'Uploaded to this channel',   'RouteCallApp' ),
				'views'                 => __( 'Filter Channels list',       'RouteCallApp' ),
				'pagination'            => __( 'Channel list navigation',   'RouteCallApp' ),
				'list'                  => __( 'Channel list',              'RouteCallApp' ),
	
				// Labels for hierarchical post types only.
				'parent_item'        => __( 'Parent Item',                'RouteCallApp' ),
				'parent_item_colon'  => __( 'Parent Item:',               'RouteCallApp' ),
			]
		], $this->get_default_args());
	}
	
}