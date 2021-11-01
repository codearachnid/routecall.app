<?php

NAMESPACE RouteCallApp;

class PostTypes {
	private $page_title = 'Route Call Application';
	private $menu_title = 'Route Call App';
	private $menu_slug  = __NAMESPACE__;
	private $custom_post_types = [
		  'custom_post_type' => ['registered'=>false, 'class'=> 'Custom_Post_Type', 'file' => 'custom-post-type.php', 'autoregister' => true ],
		  'target' => ['registered'=>false, 'class'=> 'Target', 'file' => 'target.php', 'autoregister' => true ],
		];
		
	public function __construct(){
	  $this->auto_register();
	  add_action( 'admin_menu', [ $this,'setup_admin_menue' ] );
	}
	
	private function auto_register(){
		foreach( $this->custom_post_types as $post_type => $param ){
			if( $param['autoregister'] === true ){
				$this->register($post_type);	
			}
		}
	}
	
	private function register($cpt){
	  if( array_key_exists($cpt, $this->custom_post_types) && $this->custom_post_types[$cpt]['registered'] === false){
		  $class_folder = 'feature/post_types/';
		  $class_name = __NAMESPACE__ . '\CPT\\' . $this->custom_post_types[$cpt]['class'];
		Framework::register_file( $class_folder . $this->custom_post_types[$cpt]['file']);
		$this->custom_post_types['registered'] = new $class_name();
	  }
	  return $this->custom_post_types['registered'];
  }
  
  public function setup_admin_menue(){
	  add_menu_page(
			$this->page_title,
			$this->menu_title,
			'manage_options',
			$this->menu_slug,
			[ $this, 'routecallapp_dashboard' ],
			'dashicons-media-code',
			4
		  );
  }
  
  public function routecallapp_dashboard(){
	  echo $this->page_title;
	}
}