<?php

NAMESPACE RouteCallApp;

// use Twilio\TwiML\VoiceResponse;

class CPT_Channel {
  private $labels;
  private $args;
  const POST_TYPE = 'routecall_channel';

  public function __construct(){
      $this->labels = [
      "name" => __( "Channels", "twentytwentyone" ),
      "singular_name" => __( "Channel", "twentytwentyone" ),
      "menu_name" => __( "Channels", "twentytwentyone" ),
      "parent" => __( "routecallapp", "twentytwentyone" ),
      "parent_item_colon" => __( "routecallapp", "twentytwentyone" ),
    ];
    $this->args = [
      "label" => __( "Channels", "twentytwentyone" ),
      "labels" => $this->labels,
      "description" => "",
      "public" => true,
      "publicly_queryable" => true,
      "show_ui" => true,
      "show_in_rest" => true,
      "rest_base" => "",
      "rest_controller_class" => "WP_REST_Posts_Controller",
      "has_archive" => true,
      "show_in_menu" => __NAMESPACE__,
      "show_in_nav_menus" => true,
      "delete_with_user" => false,
      "exclude_from_search" => false,
      "capability_type" => "page",
      "map_meta_cap" => true,
      "hierarchical" => true,
      "rewrite" => [ "slug" => 'channel', "with_front" => true ],
      "query_var" => true,
      "supports" => [ "title" ],
    ];
  }

  public function register(){
    register_post_type( self::POST_TYPE, $this->args );
    add_action( 'cmb2_admin_init', [ $this, 'metabox' ]);
    add_filter( 'manage_posts_columns' , [ $this, 'manage_columns' ]);
    // add_filter( 'template_include', [ $this, 'view_template' ]);
    add_action( 'template_redirect', [ $this, 'call_handler' ]);
  }

  function manage_columns( $columns ) {
    unset($columns['date']);
    return $columns;
  }

  function call_handler( $template_redirect ){

    // TWILIO setup response
    // $response = new VoiceResponse();
    $response = null;
    $respond = false;

    if( is_post_type_archive(self::POST_TYPE) && !empty( $_REQUEST['To'] ) ){

      // echo 'is_post_type_archive(self::POST_TYPE)';
      // echo is_post_type_archive(self::POST_TYPE);
      // print_r($_REQUEST);

      // find the appropriate channel that has been most recently updated
      // TODO probably should use better criteria for filters than recently updated
      $lookup_channel = new \WP_Query( [
          'post_type' => self::POST_TYPE,
            'meta_key'     => 'inbound_number',
            'meta_value'   => $_REQUEST['To'],
            'meta_compare' => 'LIKE',
          'orderby' => 'modified',
          'post_status' => ['published'],
          'posts_per_page' => 1,
      ]);

      // print_r($lookup_channel);

      if( !empty($lookup_channel->post) ){
          $response = Framework::get_instance()->get_registered('task')->do_redirect( get_permalink($lookup_channel->post) );
      } else {
        // $response->redirect('error', ['method' => 'POST']);
        // TODO twiml error + LOGGER
      }

      $respond = true;

    } else
    if( is_singular( self::POST_TYPE ) ){
       global $post;
       $route_id = get_post_meta( $post->ID, 'route_id', true );
       $task_list = get_post_meta( $route_id, 'task_list', true );
       if(!empty($task_list)){
          $response = Framework::get_instance()->get_registered('task')->build_tasks_response( $route_id, $task_list );
          $respond = true;
       }
     }

    // determine if we should redirect with WordPress or response with API
    if($respond){
      header("Content-type: text/xml");
      echo $response;
      die;
    } else {
      return $template_redirect;
    }
  }

  function view_template( $template ){

    $view = RouteCallApp_DIR . 'assets/views/twiml.php';
    if( get_post_type() == self::POST_TYPE && file_exists( $view ) ) {
        $template = $view;
    }

    // Return template
    return $template;
  }

  public function metabox(){

  	$metabox = new_cmb2_box([
  		'id'            => 'routecallapp_channel_metabox',
  		'title'         => esc_html__( 'Channel Settings', __NAMESPACE__ ),
  		'object_types'  => [ self::POST_TYPE ],
  		'priority'   => 'high',
      'show_names'   => false, // Show field names on the left
  	]);

    $route = new CPT_Route();
    $routes = wp_list_pluck( $route->get_all(), 'post_title', 'ID' );

    $metabox->add_field([
      'name' => esc_html__( 'Inbound number', __NAMESPACE__ ),
      'desc' => esc_html__( 'The phone number or client identifier of the called party.', __NAMESPACE__ ),
      'id'   => 'inbound_number',
      'type' => 'text',
      'column' => [
          'position' => 3,
          'name'     => 'Inbound',
      ],
      'repeatable' => true,
    ]);


    $metabox->add_field([
  		'name'             => __( 'Select Route', 'routecallapp' ),
  		'desc'             => __( 'Select a route workflow for this channel', 'routecallapp' ),
  		'id'               => 'route_id',
  		'type'             => 'select',
  		'show_option_none' => 'No route selected.',
      'column'  => [
          'position' => 2,
          'name'     => 'Selected Route',
      ],
      'display_cb' => [ $this, 'route_display_link' ],
  		'options'          => $routes,
  	]);


    $logs = new_cmb2_box([
     'id'            => 'routecallapp_channel_logs',
     'title'         => esc_html__( 'Logs', 'routecallapp' ),
     'object_types'  => [ CPT_Log::POST_TYPE ], // Post type
     'context' => 'side',
     'priority'   => 'low',
   ]);


  }


  function route_display_link( $field, $data ){
    $html = '';
    $i = 0;
    foreach($field['render_row_cb'] as $field_data ){
      if( is_object( $field_data ) ){
        if( !empty($field_data->args( 'options' )[ $field_data->value ]) ){
          $i++;
          $html .= sprintf('%s. <a href="%s">%s</a>',
            $i,
            get_edit_post_link($field_data->value),
            $field_data->args( 'options' )[ $field_data->value ]
          );
        }
      }
    }
    return $html;
  }



}
