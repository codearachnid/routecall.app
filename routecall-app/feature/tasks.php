<?php

NAMESPACE RouteCallApp;

use Twilio\TwiML\VoiceResponse;
// use Twilio\TwiML\MessagingResponse;

class Tasks {

  private $task_lib = [
	  'task' => ['registered'=>false, 'class'=> 'Task', 'file' => 'task.php' ],
	  'block_caller' => ['registered'=>false, 'class'=> 'Task_Block_Caller', 'file' => 'block-caller.php' ],
	  'hangup' => ['registered'=>false, 'class'=> 'Task_Hangup', 'file' => 'hangup.php' ],
	  'target_lookup' => ['registered'=>false, 'class'=> 'Task_Target_Lookup', 'file' => 'target-lookup.php' ],
	  'play_audio' => ['registered'=>false, 'class'=> 'Task_Play_Audio', 'file' => 'play-audio.php' ],
	  'speak_text' => ['registered'=>false, 'class'=> 'Task_Speak_Text', 'file' => 'speak-text.php' ],
	  'gather_input' => ['registered'=>false, 'class'=> 'Task_Gather_Input', 'file' => 'gather-input.php' ],
	  'redirect_route' => ['registered'=>false, 'class'=> 'Task_Redirect_Route', 'file' => 'redirect-route.php' ],
	  'direct_dial' => ['registered'=>false, 'class'=> 'Task_Direct_Dial', 'file' => 'direct-dial.php' ],
	  'gather_input_menu' => ['registered'=>false, 'class'=> 'Task_Gather_Input_Menu', 'file' => 'gather-input-menu.php' ],
	  'action_menu' => ['registered'=>false, 'class'=> 'Task_Action_Menu', 'file' => 'action-menu.php' ],
	  'send_sms' => ['registered'=>false, 'class'=> 'Task_Send_Sms', 'file' => 'send-sms.php' ],
	  'gather_voicemail' => ['registered'=>false, 'class'=> 'Task_Gather_Voicemail', 'file' => 'gather-voicemail.php' ],
	];

  public function __construct(){
	  $this->register('task');
  }
  
  private function register($task){
	  if( array_key_exists($task, $this->task_lib) && $this->task_lib[$task]['registered'] === false){
		  $class_folder = 'feature/tasks/';
		  $class_name = __NAMESPACE__ . '\Tasks\\' . $this->task_lib[$task]['class'];
		Framework::register_file( $class_folder . $this->task_lib[$task]['file']);
		$this->task_lib['registered'] = new $class_name();
	  }
	  return $this->task_lib['registered'];
  }

  public function build_tasks_response( $route_id, $selected_tasks ){
	$route_config = get_post_meta( $route_id );
	$r = new VoiceResponse();
	foreach( $selected_tasks as $selected_task ){
		$task_key = $selected_task['acf_fc_layout'];
		if( array_key_exists($task_key, $this->task_lib)){
			Framework::get_instance()->track()->event(['action'=> __CLASS__ . '.' . __FUNCTION__ . '.build_tasks_response.' . $task_key, 'raw' => $selected_task]);
			$this->register($task_key)->output( $selected_task, $r );
		}
	}
	return $r;
  }
  
  public function redirect( $url ){
	  $r = new VoiceResponse();
	  return $this->register('redirect_route')->output(['url'=>$url], $r);
  }
  public function block_caller(){
	  return $this->register(__FUNCTION__)->output();
  }
  

}
