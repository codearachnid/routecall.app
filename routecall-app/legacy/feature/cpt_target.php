<?php

NAMESPACE RouteCallApp;

class CPT_Target {
  private $labels;
  private $args;
  private $actions;
  const POST_TYPE = 'routecall_target';


  public function __construct(){
      $this->labels = [
      "name" => __( "Targets", "twentytwentyone" ),
      "singular_name" => __( "Target", "twentytwentyone" ),
      "menu_name" => __( "Targets", "twentytwentyone" ),
      "parent" => __( "routecallapp", "twentytwentyone" ),
      "parent_item_colon" => __( "routecallapp", "twentytwentyone" ),
    ];
    $this->args = [
      "label" => __( "Targets", "twentytwentyone" ),
      "labels" => $this->labels,
      "description" => "",
      "public" => true,
      "publicly_queryable" => true,
      "show_ui" => true,
      "show_in_rest" => true,
      "rest_base" => "",
      "rest_controller_class" => "WP_REST_Posts_Controller",
      "has_archive" => false,
      "show_in_menu" => __NAMESPACE__,
      "show_in_nav_menus" => true,
      "delete_with_user" => false,
      "exclude_from_search" => false,
      "capability_type" => "page",
      "map_meta_cap" => true,
      "hierarchical" => true,
      "rewrite" => [ "slug" => self::POST_TYPE, "with_front" => true ],
      "query_var" => true,
      "supports" => [ "title" ],
    ];

  }

  public function register(){
    register_post_type( self::POST_TYPE, $this->args );
    add_action( 'cmb2_admin_init', [ $this, 'metabox' ]);
  }

  public function metabox(){

  	$metabox = new_cmb2_box([
  		'id'            => 'routecallapp_target_metabox',
  		'title'         => esc_html__( 'Settings', 'routecallapp' ),
  		'object_types'  => [ self::POST_TYPE ],
  		'priority'   => 'high',
      // 'show_names'   => false, // Show field names on the left
  	]);

    $metabox->add_field([
      'name' => esc_html__( 'Zipcodes', __NAMESPACE__ ),
      'desc' => esc_html__( 'One number per line to be recognized by the system in query', __NAMESPACE__ ),
      'id'   => 'zipcode',
      'type' => 'text',
      'column' => true,
      'repeatable' => true,
    ]);

    /***************************************************************************/

    $associate_data = new_cmb2_box([
  		'id'           => 'cmb2_attached_posts_field',
  		'title'        => __( 'Associated Data', 'yourtextdomain' ),
  		'object_types' => [ self::POST_TYPE ], // Post type
  		'context'      => 'normal',
  		'priority'     => 'high',
  		'show_names'   => false, // Show field names on the left
  	]);

    $associate_data->add_field([
      'name'    => __( 'Attached Channels', 'yourtextdomain' ),
      'desc'    => __( 'Drag channels from the left column to the right column to attach them to this page.<br />You may rearrange the order of the posts in the right column by dragging and dropping.', 'yourtextdomain' ),
      'id'      => 'associated_channel',
      'type'    => 'custom_attached_posts',
      'column'  => true, // Output in the admin post-listing as a custom column. https://github.com/CMB2/CMB2/wiki/Field-Parameters#column
      'options' => [
        'show_thumbnails' => true, // Show thumbnails on the left
        'filter_boxes'    => true, // Show a text box for filtering the results
        'query_args'      => [
          'posts_per_page' => 10,
          'post_type'      => CPT_Channel::POST_TYPE,
        ], // override the get_posts args
      ],
    ]);

    $associate_data->add_field([
      'name'    => __( 'Attached Users', 'yourtextdomain' ),
      'desc'    => __( 'Drag users from the left column to the right column to attach them to this page.<br />You may rearrange the order of the users in the right column by dragging and dropping.', 'yourtextdomain' ),
      'id'      => 'contact',
      'type'    => 'custom_attached_posts',
      'column'  => true, // Output in the admin post-listing as a custom column. https://github.com/CMB2/CMB2/wiki/Field-Parameters#column
      'display_cb' => [ $this, 'target_contact_link' ],
      'options' => [
        'show_thumbnails' => true, // Show thumbnails on the left
        'filter_boxes'    => true, // Show a text box for filtering the results
        'query_users'     => true, // Do users instead of posts/custom-post-types.
      ],
    ]);

    /***************************************************************************/

     $billing = new_cmb2_box([
   		'id'            => 'routecallapp_target_billing',
   		'title'         => esc_html__( 'Billing Details', 'routecallapp' ),
   		'object_types'  => [ self::POST_TYPE ], // Post type
      'context' => 'side',
   		'priority'   => 'low',
   	]);
    $billing->add_field([
      'name' => esc_html__( 'Billing Name (attn)', __NAMESPACE__ ),
      'id'   => 'billing_name',
      'type' => 'text',
      // 'show_names'   => false, // Show field names on the left
    ]);
    // Address field
	$billing->add_field([
		'name'       => __( 'Billing Address', 'cmb2' ),
		'desc'       => __( 'field description (optional)', 'cmb2' ),
		'id'         => 'billing_address',
		'type'       => 'address',
    'show_label'   => false, // Show field names on the left
		// 'repeatable'      => true,
	]);

     $notes = new_cmb2_box([
   		'id'            => 'routecallapp_target_notes',
   		'title'         => esc_html__( 'Notes', 'routecallapp' ),
   		'object_types'  => [ self::POST_TYPE ], // Post type
   		'priority'   => 'low',
   	]);

    // TODO: note ['user_id', 'date', 'data']


  }

  // TODO format this into cmb2-user-search
  function target_contact_link( $field, $data ){
    $html = '';
    $i=0;
    if( !empty($data->value) ){
      foreach( $data->value as $user_id ){
        $i++;
        $user = get_userdata( $user_id );
        $html .= sprintf('%s. <a href="%s">%s</a> (%s)<br />',
          $i,
          get_edit_user_link($user_id),
          $user->display_name,
          $user->user_login
        );
      }
    }
    return $html;
  }


}
