<?php
/**
 * @package	CMB2\Field_Phone
 * @author 	@codearachnid
 * @copyright	Copyright (c) Timothy Wood @codearachnid
 *
 * Plugin Name: CMB2 Field Type: Phone
 * Plugin URI: https://github.com/scottsawyer/cmb2-field-address
 * Github Plugin URI: https://github.com/scottsawyer/cmb2-field-address
 * Description: CMB2 field type to create an address.
 * Version: 1.0
 * Author: scottsawyer
 * Author URI: https://www.scottsawyerconsulting.com
 * License: GPLv2+
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'CMB2_Field_Phone' ) ) {
  /**
   * Class CMB2_Field_Address
   */
  class CMB2_Field_Phone {

    /**
     * Current version number
     */
    const VERSION = '1.0.0';

    /**
     * Initialize the plugin
     */
    public function __construct() {
      add_action( 'cmb2_render_phone', [$this, 'render_phone'], 10, 5 );
      // add_filter( 'cmb2_sanitize_phone', [$this, 'maybe_save_split_values'], 12, 4 );
      add_filter( 'cmb2_sanitize_phone', [$this, 'sanitize'], 10, 5 );
      add_filter( 'cmb2_types_esc_phone', [$this, 'escape'], 10, 4 );
    }

    //public static function class_name() { return __CLASS__; }

		/**
		 * Handles outputting the phone field.
		 */
    public static function render_phone( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {

      // $args = empty( $args ) ? $this->args : $args;
  		// $a = $this->parse_args( $this->type, array(
  		// 	'type'            => 'text',
  		// 	'class'           => 'regular-text',
  		// 	'name'            => $this->_name(),
  		// 	'id'              => $this->_id(),
  		// 	'value'           => $this->field->escaped_value(),
  		// 	'desc'            => $this->_desc( true ),
  		// 	'js_dependencies' => array(),
  		// ), $args );
      //
      // $html = sprintf( '<input%s/>%s', $this->concat_attrs( $a, array( 'desc' ) ), $a['desc'] );
// var_dump($field_type_object);
			echo $field_type_object->input( [
					'type'	=> 'text',
					'name'  => $field_type_object->_name( 'phone' ),
          'placeholder' => '(###) ###-####',
					'id'    => $field_type_object->_id( '_phone' ),
					'value' => $field_escaped_value,
          'classes' => 'text_phone',
					'desc'  => '',
				]);

			echo $field_type_object->_desc( 'true' );

      ?><script type="text/javascript">
      jQuery(document).ready(function($){

  $(".text_phone").mask("(999) 999-9999");


  $(".text_phone").on("blur", function() {
      var last = $(this).val().substr( $(this).val().indexOf("-") + 1 );
      if( last.length == 5 ) {
          var move = $(this).val().substr( $(this).val().indexOf("-") + 1, 1 );
          var lastfour = last.substr(1,4);
          var first = $(this).val().substr( 0, 9 );
          $(this).val( "(" + first + ") " + move + '-' + lastfour );
      }
  });
});
</script><?php

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
	$cmb2_field_phone = new CMB2_Field_Phone();
}
