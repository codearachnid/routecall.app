<?php

NAMESPACE RouteCallApp;

class Framework{

   private static $instance;
   private $page_title = 'Route Call Application';
   private $menu_title = 'Route Call App';
   private $menu_slug  = __NAMESPACE__;
   private $registered = [];
   private $vendor_lib = [
     'twilio-php-main/src/Twilio/autoload.php',
     ];
     
  private $call_sid = null;
     
  private $framework_lib = [
    'utils' => ['class'=> __NAMESPACE__ . '\Utils', 'file' => 'feature/utilities.php', 'autoregister' => true ],
    'data' => ['class'=> __NAMESPACE__ . '\Data', 'file' => 'feature/data.php', 'autoregister' => true ],
    'tasks' => ['class'=> __NAMESPACE__ . '\Tasks', 'file' => 'feature/tasks.php', 'autoregister' => true ],
    'track' => ['class'=> __NAMESPACE__ . '\Track', 'file' => 'feature/track.php', 'autoregister' => true ],
    'callback' => ['class'=> __NAMESPACE__ . '\Callback', 'file' => 'feature/callback.php', 'autoregister' => false ],
  ];

  const CHANNEL_POST_TYPE = 'routecall_channel';
  const ROUTE_POST_TYPE = 'routecall_route';

  public function __construct() {
    $this->init_vendor();
    add_action( 'admin_menu', array($this,'setup_admin_menue') );
    add_filter( 'template_redirect', array( $this, 'route_template'), 99 );
    add_action( 'pre_get_posts', array( $this, 'sort_cpt_by_order'), 1 );
  }
  
  function route_template( $template_redirect ) {
    // TWILIO setup response
    // $response = new VoiceResponse();
    $response = null;
    $respond = false;
    
        
    if( empty($this->call_sid) ){
      $this->call_sid = $this->track()->generate_sid();
    }
    
    // init inbound api req logging
    $this->track()->set_call_sid( $this->call_sid );
    $this->track()->api_log($this->call_sid, $_REQUEST);
    
    $is_default_page = (bool) (get_field('call_api_landing_page', 'option')->ID == get_the_ID());
    $is_callback_page = (bool) (get_field('call_api_callback_page', 'option')->ID == get_the_ID());
    
    if( $this->get_data()->is_caller_blocked( Utils::get_header_param('From') ) ){
      $respond = true;
      $response = $this->get_registered('tasks')->block_caller();
      // TODO track blocked caller attempt to CPT
      $this->track()->event(['action'=> __CLASS__ . '.' . __FUNCTION__ . '.block_call', 'raw' => Utils::get_header_param('ALL')]);
    } else if($is_callback_page){
      $status = $this->get_data()->update_recording_log( Utils::get_header_param('recording_callback') );
      $respond = true;
      $xml = new \SimpleXMLElement('<Response/>');
      Utils::to_xml($xml, array_merge([
        'Status' => $status ? 'Success' : 'Failure'
      ],Utils::get_header_param('ALL')));
      $response = $xml->asXML();
    } else if( $is_default_page || is_post_type_archive(self::CHANNEL_POST_TYPE) ){
    
      $route = get_field('call_api_default_channel','option');

      if( empty($route) ) {
        // find the appropriate channel that has been most recently updated
        // TODO probably should use better criteria for filters than recently updated
        $channel = $this->get_data()->get_channel_by_inbound( Utils::get_header_param('To') );
        $route = $this->get_data()->get_channel_route( $channel );
        $this->track()->event(['action'=> __CLASS__ . '.' . __FUNCTION__ . '.channel', 'raw' => $channel]);
        $this->track()->event(['action'=> __CLASS__ . '.' . __FUNCTION__ . '.channel_ID', 'raw' => $channel->ID]);
      } else {
        $this->track()->event(['action'=> __CLASS__ . '.' . __FUNCTION__ . '.default_channel_ID', 'raw' => $route]);
      }
      
      if( !empty($route) ){
        $response = $this->get_registered('tasks')->redirect($route); //do_redirect( get_permalink($route) );
        $respond = true;
        $this->track()->event(['action'=> __CLASS__ . '.' . __FUNCTION__ . '.route_ID', 'raw' => $route]);
      } else {
        // $response->redirect('error', ['method' => 'POST']);
        // TODO twiml error + LOGGER
        $this->track()->event(['action'=> __CLASS__ . '.' . __FUNCTION__ . '.error_route_redirect']);
      }
      
    } else if ( get_post_type( get_the_ID() ) == self::CHANNEL_POST_TYPE  ) {
      $channel = $this->get_data()->get_channel_by_inbound( Utils::get_header_param('To') );
      $route = $this->get_data()->get_channel_route( $channel );
      $response = $this->get_registered('tasks')->redirect($route); //do_redirect( get_permalink($route) );
      $respond = true;
      $this->track()->event($this->call_sid, ['action'=> __CLASS__ . '.' . __FUNCTION__ . '.channel', 'raw' => $channel]);
      $this->track()->event($this->call_sid, ['action'=> __CLASS__ . '.' . __FUNCTION__ . '.channel_ID', 'raw' => $channel->ID]);
      $this->track()->event($this->call_sid, ['action'=> __CLASS__ . '.' . __FUNCTION__ . '.route_ID', 'raw' => $route]);
    } else if ( get_post_type( get_the_ID() ) == 'routecall_route'  ) {
        $task_list = get_field( 'task_list' );
        $response = $this->get_registered('tasks')->build_tasks_response( get_the_ID(), $task_list );
        $respond = true;
        $this->track()->event(['action'=> __CLASS__ . '.' . __FUNCTION__ . '.route_ID', 'raw' => get_the_ID() ]);
        $this->track()->event(['action'=> __CLASS__ . '.' . __FUNCTION__ . '.build_task_response', 'raw' => $task_list ]);
    }
    // determine if we should redirect with WordPress or response with API
    if($respond){
      header("Content-type: text/xml");
      echo trim($response);
      $this->track()->event(['action'=> __CLASS__ . '.' . __FUNCTION__ . '.api_respond', 'raw' => trim($response)]);
      die;
    } else {
      return $template_redirect;
    }
  }

  public function init_vendor(){
    foreach( $this->vendor_lib as $vendor_path ){
      $this->register_file('vendor/' .  $vendor_path);
    }
  }

  public static function bootstrap(){
    // TODO best way to include twilio????
    // TODO: should I install now or startup
    foreach( $this->framework_lib as $lib => $param ){
      self::register_file($param['file']);
      if( $param['autoregister'] ){
        $this->registered[$lib] = new $param['class']();
      }
    }
    $this->call_sid = Utils::get_header_param('CallSid');
  }
  
  public function track(){
    return $this->get_registered('track');
  }

  public function get_data(){
    return $this->get_registered('data');
  }
  
  public function get_registered( $key ){
    if( !empty($this->registered[ $key ]) ){
        return $this->registered[ $key ];
    } else if ( key_exists($key, $this->framework_lib) ) {
      self::register_file( $this->framework_lib[$key]['file'] );
      $this->registered[$key] = new $this->framework_lib[$key]['class']();
      return $this->registered[$key];
    } else {
      return null;
    }

  }
  
  public static function register_file( $path = null ){
    if( !is_null($path) && file_exists( RouteCallApp_DIR . $path )){
      require_once RouteCallApp_DIR . $path;
      return true;
    }
    return false;
  }


  function setup_admin_menue(){
    add_menu_page(
      $this->page_title,
      $this->menu_title,
      'manage_options',
      $this->menu_slug,
      array( $this, 'routecallapp_dashboard' ),
      'dashicons-media-code',
      4
    );
  }

  public function routecallapp_dashboard(){
    echo $this->page_title;
  }


  public static function install(){
    if( !class_exists('RouteCallApp_Install' ) ){
      require RouteCallApp_DIR . 'feature/install.php';
    }
    $install = new RouteCallApp_Install();
    $install->createTable();
    return $install;
  }
  
  function sort_cpt_by_order( $q ){
    
      // exit out if it is NOT the admin or it isn't the main query
      if ( ! is_admin() || ! $q->is_main_query() ) {
        return;
      }
      $s = get_current_screen();
      // change 'book' with your real CPT name
      if ( $s->base === 'edit' && $s->post_type === 'routecall_route' ) {
        $q->set('orderby', 'menu_order');
        $q->set('order', 'ASC');
      }
    
  }
  
  public function get_the_sid(){
    return $this->call_sid;
  }

  /**
     * Gets an instance of our plugin.
     *
     * @return WP_Kickass_Plugin
     */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
