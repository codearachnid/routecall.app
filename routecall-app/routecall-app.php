<?php
/**
 * @package RouteCallApp
 * @version 0.1.0
 */
/*
Plugin Name: RouteCall.APP
Plugin URI: https://alwayscurious.co
Description: Customize the RouteCall.app logic
Author: Timothy Wood @codearachnid
Version: 0.1.0
Author URI: https://codearachnid.com
*/

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( ! defined( 'RouteCallApp_DIR' ) ){
	define( 'RouteCallApp_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'RouteCallApp_NAMESPACE' ) ) {
	define( 'RouteCallApp_NAMESPACE', 'RouteCallApp' );
}

if( file_exists(RouteCallApp_DIR . 'feature/framework.php') ){
	require_once RouteCallApp_DIR . 'feature/framework.php';
}

if( class_exists( 'RouteCallApp\Framework') ){

	// TODO: run this when plugin is activated or direct action
	// assume plugin is installed correctly for now
  //$RouteCallApp->install();



  add_action( 'init', [ RouteCallApp\Framework::get_instance(),'bootstrap' ] );



}
