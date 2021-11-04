<?php

NAMESPACE RouteCallApp;

class PostTypes {
	private $page_title = 'Route Call Application';
	private $menu_title = 'Route Call App';
	private $menu_slug  = 'RouteCallApp';
	private $custom_post_types = [
		  'custom_post_type' => ['registered'=>false, 'class'=> 'Custom_Post_Type', 'file' => 'custom-post-type.php', 'autoregister' => true ],
		  'custom_taxonomy' => ['registered'=>false, 'class'=> 'Custom_Taxonomy', 'file' => 'custom-taxonomy.php', 'autoregister' => true ],
		  'target' => ['registered'=>false, 'class'=> 'Target', 'file' => 'target.php', 'autoregister' => true ],
		  'route' => ['registered'=>false, 'class'=> 'Route', 'file' => 'route.php', 'autoregister' => true ],
		  'channel' => ['registered'=>false, 'class'=> 'Channel', 'file' => 'channel.php', 'autoregister' => true ],
		  'log' => ['registered'=>false, 'class'=> 'Log', 'file' => 'log.php', 'autoregister' => true ],		  
		  'blocked' => ['registered'=>false, 'class'=> 'Blocked', 'file' => 'blocked.php', 'autoregister' => true ],
		  'services' => ['registered'=>false, 'class'=> 'Services', 'file' => 'services.php', 'autoregister' => true ],
		  'voices' => ['registered'=>false, 'class'=> 'Voices', 'file' => 'voices.php', 'autoregister' => true ],
		];
		
	public function __construct(){
	  add_action( 'init', [ $this, 'auto_register' ] );
	}
	
	public function auto_register(){
		$this->setup_menu();
		foreach( $this->custom_post_types as $post_type => $param ){
			if( $param['autoregister'] === true ){
				$this->register($post_type);	
			}
		}
	}
	
	public function register($cpt){
	  if( array_key_exists($cpt, $this->custom_post_types) && $this->custom_post_types[$cpt]['registered'] === false){
		  $class_folder = 'feature/post_types/';
		  $class_name = __NAMESPACE__ . '\CPT\\' . $this->custom_post_types[$cpt]['class'];
		Framework::register_file( $class_folder . $this->custom_post_types[$cpt]['file']);
		$this->custom_post_types['registered'] = new $class_name();
	  }
	  return $this->custom_post_types['registered'];
  }
  
  public function setup_menu(){
	  add_menu_page(
		$this->page_title,
		$this->menu_title,
		'manage_options',
		$this->menu_slug,
		[ $this, 'default_dashboard' ],
		'dashicons-networking',
		1
	  );
  }
  
  	
  public function default_dashboard(){
	  echo $this->page_title;
  }

}