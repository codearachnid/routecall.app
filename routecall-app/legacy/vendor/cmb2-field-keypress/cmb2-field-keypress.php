<?php
/**
 * @package	CMB2\Field_Keypress
 * @author 	@codearachnid
 * @copyright	Copyright (c) Timothy Wood @codearachnid
 *
 * Plugin Name: CMB2 Field Type: Keypress
 * Plugin URI: https://github.com/scottsawyer/cmb2-field-address
 * Github Plugin URI: https://github.com/scottsawyer/cmb2-field-address
 * Description: CMB2 field type to create an address.
 * Version: 1.0
 * Author: scottsawyer
 * Author URI: https://www.scottsawyerconsulting.com
 * License: GPLv2+
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'CMB2_Field_Keypress' ) ) {
  /**
   * Class CMB2_Field_Address
   */
  class CMB2_Field_Keypress {

    /**
     * Current version number
     */
    const VERSION = '1.0.0';

    /**
     * Initialize the plugin
     */
    public function __construct() {
      add_action( 'cmb2_render_keypress', [$this, 'render'], 10, 5 );
      // add_filter( 'cmb2_sanitize_phone', [$this, 'maybe_save_split_values'], 12, 4 );
      add_filter( 'cmb2_sanitize_keypress', [$this, 'sanitize'], 10, 5 );
      add_filter( 'cmb2_types_esc_keypress', [$this, 'escape'], 10, 4 );
    }

    //public static function class_name() { return __CLASS__; }

		/**
		 * Handles outputting the phone field.
		 */
    public static function render( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {


// var_dump($field_type_object);
// var_dump($field_escaped_value);

  $i=0;
  echo '<ul>';
  while($i<10){

    $args = [
      'iterator' => 0,
      'name' => $field->args['_name'] . '['.$i.']',
      'id' => $field->args['_id'],
      'options' => $field->options(),
      'attributes' => $field->args['attributes'],
    ];

    if(!empty($field_escaped_value[ $i ])){
      $args['selected_value'] = $field_escaped_value[ $i ];
    }

    $dropdown_html = $this->render_task_dropdown( $args );

    printf('<li><div>Press %s</div><div>%s</div></li>', $i, $dropdown_html );
    $i++;
  }
  echo '</ul>';
  echo $field_type_object->_desc( true );

    }

    public function render_task_dropdown( $args ){
      $args = wp_parse_args( $args, [
        'name' => null,
        'id' => null,
        'options' => [],
        'attributes' => [],
        'selected_value' => null
      ]);
      $attr = array_map(function($value, $key) {
          return $key.'="'.$value.'"';
      }, array_values($args['attributes']), array_keys($args['attributes']));
      $attr = implode($attr, "");

      $options='';

      foreach ( $args['options'] as $value => $name ) {
        $options .= sprintf('<option class="cmb2-option" value="%s" %s>%s</option>',
          esc_attr( $value ),
          selected( $args['selected_value'], $value, false ),
          esc_html( $name ));
      }

      return sprintf( '<select name="%s" id="%s" %s /><option class="cmb2-option" value="">Select an action for keypress</option>%s</select>',
        $args['name'],
        $args['id'],
        $attr,
        $options
      );

    }

		public static function sanitize( $check, $meta_value, $object_id, $field_args, $sanitize_object ) {
			// if not repeatable, bail out.
			if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
				return $check;
			}
			foreach ( $meta_value as $key => $val ) {
				$meta_value[ $key ] = array_filter( array_map( 'sanitize_text_field', $val ) );
			}
			return array_filter($meta_value);
		}


		public static function escape( $check, $meta_value, $field_args, $field_object ) {
			// if not repeatable, bail out.
			if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
				return $check;
			}
			foreach ( $meta_value as $key => $val ) {
				$meta_value[ $key ] = array_filter( array_map( 'esc_attr', $val ) );
			}
			return array_filter($meta_value);
		}
	}
	$cmb2_field_keypress = new CMB2_Field_Keypress();
}
