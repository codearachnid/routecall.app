<?php

NAMESPACE RouteCallApp;

class CPT_Log {
  private $labels;
  private $args;
  private $actions;
  const POST_TYPE = 'routecall_log';
  const TBL_DETAIL = 'routecallapp_event_detail';


  public function __construct(){
      $this->labels = [
      "name" => __( "Logs", "twentytwentyone" ),
      "singular_name" => __( "Log", "twentytwentyone" ),
      "menu_name" => __( "Logs", "twentytwentyone" ),
      "parent" => __( "routecallapp", "twentytwentyone" ),
      "parent_item_colon" => __( "routecallapp", "twentytwentyone" ),
    ];
    $this->args = [
      "label" => __( "Logs", "twentytwentyone" ),
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


  }


}
