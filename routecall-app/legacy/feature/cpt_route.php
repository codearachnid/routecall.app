<?php
NAMESPACE RouteCallApp;

class CPT_Route
{
    private $labels;
    private $args;
    const POST_TYPE = 'routecall_route';

    public function __construct()
    {
        $this->labels = [
          "name" => __("Routes", "twentytwentyone") ,
          "singular_name" => __("Route", "twentytwentyone") ,
          "menu_name" => __("Routes", "twentytwentyone") ,
          "parent" => __("routecallapp", "twentytwentyone") ,
          "parent_item_colon" => __("routecallapp", "twentytwentyone") ,
        ];
        $this->args = [
          "label" => __("Routes", "twentytwentyone") ,
          "labels" => $this->labels,
          "description" => "",
          "public" => true,
          "publicly_queryable" => true,
          "show_ui" => true,
          "show_in_rest" => true,
          "rest_base" => "",
          "rest_controller_class" =>
          "WP_REST_Posts_Controller",
          "has_archive" => false,
          "show_in_menu" => __NAMESPACE__,
          "show_in_nav_menus" => true,
          "delete_with_user" => false,
          "exclude_from_search" => false,
          "capability_type" => "page",
          "map_meta_cap" => true,
          "hierarchical" => true,
          "rewrite" => [ "slug" => 'route', "with_front" => true],
          "query_var" => true,
          "supports" => ["title"],
        ];
    }

    public function register()
    {
        register_post_type( self::POST_TYPE, $this->args );
        add_action('cmb2_admin_init', [ $this, 'metabox_route_config' ]);
        add_action('cmb2_admin_init', [ $this, 'metabox_task_config' ]);
        add_action( 'template_redirect', [ $this, 'call_handler' ]);
    }

    function call_handler( $template_redirect ){

      $response = null;
      $respond = false;

      if( is_post_type_archive(self::POST_TYPE) && !empty( $_REQUEST['To'] ) ){

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
         $task_list = get_post_meta( $post->ID, 'task_list', true );
         if(!empty($task_list)){
            $response = Framework::get_instance()->get_registered('task')->build_tasks_response( $post->ID, $task_list );
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

    public function metabox_route_config()
    {
        $metabox_task_select = new_cmb2_box([
            'id' => 'routecallapp_route_config',
            'title' => esc_html__('Route Configuration', 'routecallapp') ,
            'object_types' => [ self::POST_TYPE ],
            'priority' => 'high',
            'show_names' => false,

        ]);

        $task_select_field_group = $metabox_task_select->add_field([
            'id' => 'task_list',
            'type' => 'group',
            'column' => [
              'position' => 2,
              'name' => 'Tasks',
            ],
            'display_cb' => [ $this, 'column_admin_display_tasks' ],
            'description' => __('Create and order the tasks of this route', 'cmb2') ,
            'options' => [
                'group_title' => __('Task {#}', 'cmb2') , // since version 1.1.4, {#} gets replaced by row number
                'add_button' => __('Add Another Task', 'cmb2') ,
                'remove_button' => __('Remove Task', 'cmb2') ,
                'sortable' => true,
                'closed' => true,
            ],
        ]);

        //
        // $metabox_task_select->add_group_field( $task_select_field_group, array(
        //     'id'   => 'task_hash',
        //     'default_cb' => array($this,'generate_default_id'),
        //     'type' => 'hidden',
        // ) );


        $task_list = Framework::get_instance()->get_registered('task')->get_tasks();
        $task_list_options = [];
        foreach( $task_list as $task_id => $task ){
          $task_list_options[ $task_id ] = $task->properties['name'];
        }

        $metabox_task_select->add_group_field($task_select_field_group, [
            'name' => __('Task Item', 'routecallapp') ,
            'desc' => __('Select tasks, move in order of delivery, you must save before proceeding to set the type which drives the settings logic lower.', 'routecallapp') ,
            'id' => 'task_id',
            'type' => 'select',
            'show_option_none' => 'No task type selected.',
            'options' => $task_list_options,
        ]);

    }

    public function metabox_task_config()
    {

            $cmb = new_cmb2_box([
                'id' => 'routecallapp_task_config',
                'title' => esc_html__('Task Settings', 'routecallapp') ,
                'show_on_cb' => [ $this, 'show_task_config' ],
                'object_types' => [ self::POST_TYPE ],
                'priority' => 'high',
            ]);

            // Add dynamic fields during view and save
            add_action('cmb2_init_hookup_routecallapp_task_config', [ $this, 'add_task_settings_fields' ]);
            add_action('cmb2_post_process_fields_routecallapp_task_config', [ $this, 'add_task_settings_fields' ]);
    }

    public function add_task_settings_fields($cmb)
    {
        $selected_tasks = get_post_meta($cmb->object_id() , 'task_list', true); //
        $task_list = Framework::get_instance()->get_registered('task')->get_tasks();
        $position = 1;
        if( $selected_tasks ){
          foreach ($selected_tasks as $selected_task) {

                  if ( isset( $task_list[ $selected_task['task_id'] ] ) )
                  {
                    // store hidden field of data
                    // $cmb->add_field( array(
                    //     'id'   => 'task_' . $task_list[ $selected_task['task_id'] ]->ID . '_properties_' . $position,
                    //     'default' => serialize($task_list[ $selected_task['task_id'] ]),
                    //     'type' => 'hidden',
                    // ) );
                      $field_args = array(
                          'id' => 'task_' . $selected_task['task_id'] . '_' . $position,
                      );
                      $field_args = wp_parse_args($task_list[ $selected_task['task_id'] ]->properties, $field_args);
                      $cmb->add_field($field_args, $position);
                      $position++;
                  }
              }
            }
    }

    public function show_task_config(){
      $task_list = cmb2_get_field('routecallapp_route_config', 'task_list')->value();
      return !empty($task_list);
    }

    public function generate_default_id($field_args, $field)
    {
        return md5(date("Y-m-d H:i:s"));
    }

    public function column_admin_display_tasks($field, $data)
    {
        $html = '';
        $i = 0;
        if (!empty($data->value))
        {
            $task_list = Framework::get_instance()->get_registered('task')->get_tasks();
            foreach ($data->value as $selected_task)
            {
                $i++;
                $html .= sprintf('%s. %s<br />', $i, $task_list[$selected_task['task_id']]->properties['name'] );
            }
        }
        return $html;

    }

    public function get_all($post_status = 'any')
    {
        if (is_string($post_status)) $post_status = array(
            $post_status
        );
        $args = array(
            'post_type' => self::POST_TYPE,
            'post_status' => $post_status,
            'posts_per_page' => - 1
        );
        $route_query = new \WP_Query($args);
        return !empty($route_query->posts) ? $route_query->posts : [];
    }

}
