<?php

NAMESPACE RouteCallApp;

use Twilio\TwiML\VoiceResponse;

class CPT_Task {

  private $tasks;

  public function __construct(){
    $this->tasks = [
      'dial_static' => (object) [
        'action' => 'do_dial',
        'properties' => [
          'twilio_verb' => 'dial',
          'name' => 'Direct Dial',
          'type' => 'phone',
          'description' => 'During an active call, connect the current caller to another party. This is a specified outbound number to transfer the call.',
          // 'attributes' => [
          //     'type' => 'number',
          // ],
        ],
      ],
      'dial_variable' => (object) [
        'action' => 'do_dial',
        'properties' => [
          'twilio_verb' => 'dial',
          'name' => 'Variable Dial',
          'type' => 'text',
          'description' => 'During an active call, connect the current caller to another party. This will take an incoming variable to define the outbound dial.',
          'attributes'  => [
        		'readonly' => 'readonly',
        		'disabled' => 'disabled',
        	]
        ],
      ],
      'say' => (object) [
        'action' => 'do_say',
        'properties' => [
          'name' => 'Speak Text',
          'type' => 'text',
          'description' => ''
        ],
      ],
      'play' => (object) [
        'action' => 'do_play',
        'properties' => [
          'name' => 'Play Recording',
          'type' => 'file',
          'description' => '',
          'options' => [
        		'url' => false, // Hide the text input for the url
        	],
        ],
      ],
      'record' => (object) [
        'action' => 'do_record',
        'properties' => [
          'name' => 'Record Converstaion',
          'type' => 'text',
          'description' => ''
        ],
      ],
      'keypress' => (object) [
        'action' => 'do_keypress',
        'properties' => [
          'name' => 'Prompt User Key Press',
          'type' => 'keypress',
          'description' => 'Collect key press during a call.'
        ],
      ],
      'gather' => (object) [
        'action' => 'do_gather',
        'properties' => [
          'name' => 'Prompt User Input',
          'type' => 'text',
          'description' => 'Collect digits or transcribe speech during a call.'
        ],
      ],
      'hangup' => (object) [
        'action' => 'do_hangup',
        'properties' => [
          'name' => 'Hangup',
          'type' => 'title',
          'description' => ''
        ],
      ],
      'enqueue' => (object) [
        'action' => 'do_dial',
        'properties' => [
          'name' => 'Add caller into a queue',
          'type' => 'text',
          'description' => 'enqueues the current call in a call queue. Enqueued calls wait in hold music until the call is dequeued by another caller via the Dial verb or transfered out of the queue via the API Leav> verb.'
        ],
      ],
      'leave' => (object) [
        'action' => 'do_dial',
        'properties' => [
          'name' => 'Leave',
          'type' => 'text',
          'description' => 'Ends a call'
        ],
      ],
      'pause' => (object) [
        'action' => 'do_dial',
        'properties' => [
          'name' => 'Pause',
          'type' => 'text',
          'description' => ''
        ],
      ],
      'manual_redirect' => (object) [
        'action' => 'do_redirect',
        'properties' => [
          'name' => 'Manual Redirect',
          'type' => 'url',
          'description' => ''
        ],
      ],
      'route_redirect' => (object) [
        'action' => 'do_redirect',
        'properties' => [
          'name' => 'Redirect To Route',
          'type' => 'select',
          'show_option_none' => 'No route selected.',
          'description' => 'Select a predefined route to navigate.',
          // 'options_cb'          => [ new CPT_Route(), 'get_all'],
        ],
      ],
      'reject' => (object) [
        'action' => 'do_dial',
        'properties' => [
          'name' => 'Reject',
          'type' => 'text',
          'description' => ''
        ],
      ],
    ];

    $route = new CPT_Route();
    $routes = wp_list_pluck( $route->get_all(), 'post_title', 'ID' );
    $this->tasks['route_redirect']->properties['options'] = $routes;
    $this->tasks['keypress']->properties['options'] = $routes;

    // $task_list_options = [];
    // foreach( $this->tasks as $task_id => $task ){
    //   $task_list_options[ $task_id ] = $task->properties['name'];
    // }
    // $this->tasks['keypress']->properties['options'] = $task_list_options;
  }

  public function build_tasks_response( $route_id, $selected_tasks ){
    $position = 1;
    $route_config = get_post_meta( $route_id );
    $r = new VoiceResponse();
    foreach( $selected_tasks as $selected_task ){
      $meta_key = 'task_' . $selected_task['task_id'] . '_' . $position;
      $config = get_post_meta( $route_id, $meta_key, true );
      switch( $selected_task['task_id'] ){
        case 'play':
          //$response->play( $config, ['loop' => 1] );
          $this->do_play( $config, $r );
          break;
        case 'say':
          // $response->say( $config, ['voice' => 'woman', 'language' => 'en-EN']);
          $this->do_say( $config, $r );
          break;
        case 'route_redirect':
          $url = get_permalink( $config );
          $this->do_redirect( $url, $r );
          break;
        case 'hangup':
          $this->do_hangup( $r );
          break;
        case 'keypress':
          $this->do_keypress();
          break;
        default:
          break;
      }
      $position++;
    }
    return $r;
  }

  public function build_task( $key, $configuration ){

  }

  public function do_keypress( $prompt = 'Please press a number', $r = null){
    $r = $this->setup_response( $r );

    $gather = $r->gather([
      'numDigits' => 1
    ]);
    // $gather->play($prompt);
    // $gather->say($prompt);
    return $r;
  }

  public function do_hangup( $r = null ){
    $r = $this->setup_response( $r );

    $r->hangup();
    return $r;
  }

  public function do_say( $text, $r = null ){
    $r = $this->setup_response( $r );

    $r->say( $text, ['voice' => 'Polly.Joanna', 'language' => 'en-US']);
    return $r;
  }

  public function do_play( $url, $r = null ){
    $r = $this->setup_response( $r );

    $r->play( $url, ['loop' => 1] );
    return $r;
  }

  public function do_redirect( $url, $r = null ){
    $r = $this->setup_response( $r );

    $r->redirect( $url, ['method' => 'POST']);
    return $r;
  }

  public function setup_response( $r = null ){
    if( is_null( $r ) ) {
      $r = new VoiceResponse(); // TWILIO setup response
    }
    return $r;
  }

  public function get_tasks(){
    return $this->tasks;
  }


}