<?php
 
/*
 * Extension Name:	Gravity Forms To WordPoints
 * Description:		Allow for the purchase of WordPoints via a Gravity Form that will connect to its own payment gateway.
 * Extension URI:	https://web321.co/plugins/gravity-forms-wordpoints
 * Author:			dewolfe001
 * Author URI:		https://shawndewolfe.com/
 * Donate:			https://www.paypal.com/paypalme/web321co/20/

 * Version:			1.0.2
 * Text Domain:		wbgf2wpt
 * Domain Path:		/languages/
 * Namespace:		wbgf2wpt

 * @package Gravity Forms To WordPoints
 * @category Extension
 * @author dewolfe001
 
 */

define( 'SWG2W_NAME', plugin_basename( __FILE__ ));

// donation link
add_filter( 'wordpoints_module_row_meta', 'wbgf2wpt_row_meta', 10, 4 );

add_action( 'gform_field_standard_settings', array( 'WordPointsGF', 'pointstype_settings' ), 10, 2);
add_action( 'gform_editor_js', array('WordPointsGF', 'pointstype_editor_script' ), 11, 2);

function wbgf2wpt_row_meta( $module_meta, $module_file, $module_data, $status ) {    
	$module_meta[] = '<a href="' . esc_url( 'https://www.paypal.com/paypalme/web321co/20/' ) . '" target="_new">' . esc_attr__( 'Donate', 'gravity-forms-wordpoints' ) . '</a>';
    return $module_meta;
}

 /**
 * Perform actions on activation.
 *
 * @since 1.0.0
 */
function wbgf2wpt_activation() {
    /* Do stuff here ... */
	// is Gravity Forms active?
	if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
		//plugin is activated

		add_action( 'admin_notices', 'wbgf2wpt_admin_notice__success' );
	} 
	else {
		// return an error notice

		add_action( 'admin_notices', 'wbgf2wpt_admin_notice__error' );
	}

}
wordpoints_register_module_activation_hook( __FILE__, 'wbgf2wpt_activation' );

// add the Gravity Forms button

if (class_exists('GF_Field')) {
	class WordPointsGF extends GF_Field { 
		public $type = 'word_points';

		public function get_form_editor_field_title() {
			return esc_attr__('WordPoints', 'wbgf2wpt');
		}
 
		public function get_form_editor_button() {
			return [
				'group' => 'advanced_fields',
				'text'  => $this->get_form_editor_field_title(),
				'id' => 'wordPoints',
				'class' => 'wordPoints',				
				'icon' => 'gform-icon--dollar',
			];
		}
 
     	public function get_field_label_class() {
    		return 'gfield_label';
    	}
 
		public function get_form_editor_field_settings() {
			return [
				'calculation_setting',
				'conditional_logic_field_setting',
				'css_class_setting',
				'default_value_setting',
				'description_setting',
				'duplicate_setting',
				'error_message_setting',
				'label_placement_setting',
				'label_setting',
				'placeholder_setting', 
				'prepopulate_field_setting',
				'range_setting',
				'rules_setting',
				'size_setting',
				'visibility_setting'
			];
		}

		public function pointstype_settings( $position, $form_id ) {
			$points_details_input	= '<div class="ginput_complex ginput_container ginput_container_points ginput_points"><div class="points-container">
				<div class="points-placeholders">
					<span class="points-placeholder">' . esc_html__( 'Points', 'wbgf2wpt' ) . '</span>
				</div>
				<select id="point-types" name="point-types" onchange="SetFieldProperty(\'point-types\', this.value);">';

			// the foreach 
			$points = get_option('wordpoints_points_types');
			foreach ($points as $key => $point) {
				$points_details_input .= '<option value="'.$key.'">'.$point['name'].'</option>';			
			}			

			$points_details_input .= '</select></div></div>';
			
			$points_users_input	= '<div class="ginput_complex ginput_container ginput_container_points ginput_points_users"><div class="points-container">
				<div class="points-users-placeholders">
					<span class="points-users-placeholder">' . esc_html__( 'Users', 'wbgf2wpt' ) . '</span>
				</div>
				<select id="point-users" name="point-users" onchange="SetFieldProperty(\'point-users\', this.value);">';

			// the foreach 
			$points_users_input .= '<option value="-1">'.esc_html__( 'Current User', 'wbgf2wpt' ).'</option>';			

			$user_list = get_users();
			foreach ( $user_list as $user ) {
				$points_users_input .= '<option value="'.$user->id.'">'.esc_html( $user->display_name ).'</option>';			
			}

			$points_users_input .= '</select></div></div>';
			
			/*
			$points_users_input	.= '<div class="ginput_complex ginput_container ginput_container_points ginput_points_users"><div class="points-container">
				<div class="points-display-placeholders">
					<span class="points-display-placeholder">' . esc_html__( 'User Display', 'wbgf2wpt' ) . '</span>
				</div>
				<input type="checkbox" value="true" id="point-display" name="point-display" onchange="SetFieldProperty(\'point-display\', this.value);" style="min-height: 48px;">';
			$points_users_input .= '</div></div>';
			*/
			
			if ($position == 5) {
			    /*
			    // global $form;
                // print "<pre>".print_r($form, TRUE)."</pre>";   			
                
                // print "<h2>".$position."</h2>";
                $check_field = GFAPI::get_form( $form_id );
                $its_fields = $check_field['fields'];
                foreach ($its_fields as $ndx => $inner_field) {
                    if ($inner_field->type == 'word_points') {
                        // print "<pre>".print_r($inner_field, TRUE)."</pre>";   
                    }
                }
                */
                
                print '<div class="WordPoints">';
				print $points_details_input;
				print $points_users_input; 
				print '</div>';
			}
		}

		public function pointstype_editor_script() {
		  ?>

		   <script type='text/javascript'>

		   jQuery('.gfield-edit').on( "click", function() {
			 alert(jQuery('#sidebar_field_label').text());
		   });

		   // To display custom field under each type of Gravity Forms field
		   jQuery.each(fieldSettings, function(index, value) {
			 fieldSettings[index] += ", .points-container";
		   });

		   // store the custom field with associated Gravity Forms field
		   jQuery(document).bind("gform_load_field_settings", function(event, field, form){
			 
			 // save field value: Start Section B
			 jQuery("#point-types").val(field["point-types"]);
			 // End Section B

			});

		   </script>

		   <?php
		}

		public function is_conditional_logic_supported() {
			return true;
		}

		public function get_value_submission( $field_values, $get_from_post_global_var = true ) {

			$value = $this->get_input_value_submission( 'input_' . $this->id, $this->inputName, $field_values, $get_from_post_global_var );

			if ( is_array( $value ) ) {
				$value = array_map( 'trim', $value );
				foreach ( $value  as &$v ) {
					$v = trim( $v );
					$v = $this->clean_value( $v );
				}
			} else {
				$value = trim( $value );
				$value = $this->clean_value( $value );
			}

			return $value;
		}

		/**
		 * Ensures the POST value is in the correct number format.
		 *
		 * @since 2.4
		 *
		 * @param $value
		 *
		 * @return bool|float|string
		 */
		public function clean_value( $value ) {
			$value = GFCommon::clean_number( $value, 'decimal_dot' );
		}

		public function validate( $value, $form ) {

			// The POST value has already been converted from currency or decimal_comma to decimal_dot and then cleaned in get_field_value().
			$value = GFCommon::maybe_add_leading_zero( $value );

			// Raw value will be tested against the is_numeric() function to make sure it is in the right format.
			// If the POST value is an array then the field is inside a repeater so use $value.
			$raw_value = isset( $_POST[ 'input_' . $this->id ] ) && ! is_array( $_POST[ 'input_' . $this->id ] ) ? GFCommon::maybe_add_leading_zero( rgpost( 'input_' . $this->id ) ) : $value;

			$requires_valid_number = ! rgblank( $raw_value ) && ! $this->has_calculation();
			$is_valid_number       = $this->validate_range( $value ) && GFCommon::is_numeric( $raw_value, $this->numberFormat );

			if ( $requires_valid_number && ! $is_valid_number ) {
				$this->failed_validation  = true;
				$this->validation_message = empty( $this->errorMessage ) ? $this->get_range_message() : $this->errorMessage;
			} elseif ( $this->type == 'quantity' ) {
				if ( intval( $value ) != $value ) {
					$this->failed_validation  = true;
					$this->validation_message = empty( $field['errorMessage'] ) ? esc_html__( 'Please enter a valid quantity. Quantity cannot contain decimals.', 'gravityforms' ) : $field['errorMessage'];
				} elseif ( ! empty( $value ) && ( ! is_numeric( $value ) || intval( $value ) != floatval( $value ) || intval( $value ) < 0 ) ) {
					$this->failed_validation  = true;
					$this->validation_message = empty( $field['errorMessage'] ) ? esc_html__( 'Please enter a valid quantity', 'gravityforms' ) : $field['errorMessage'];
				}
			}

		}

		/**
		 * Validates the range of the number according to the field settings.
		 *
		 * @param string $value A decimal_dot formatted string
		 *
		 * @return true|false True on valid or false on invalid
		 */
		private function validate_range( $value ) {

			if ( ! GFCommon::is_numeric( $value, 'decimal_dot' ) ) {
				return false;
			}

			$numeric_min = $this->numberFormat == 'decimal_comma' ? GFCommon::clean_number( $this->rangeMin, 'decimal_comma' ) : $this->rangeMin;
			$numeric_max = $this->numberFormat == 'decimal_comma' ? GFCommon::clean_number( $this->rangeMax, 'decimal_comma' ) : $this->rangeMax;

			if ( ( is_numeric( $numeric_min ) && $value < $numeric_min ) ||
				 ( is_numeric( $numeric_max ) && $value > $numeric_max )
			) {
				return false;
			} else {
				return true;
			}
		}

		public function get_range_message() {
			$min     = $this->rangeMin;
			$max     = $this->rangeMax;

			$numeric_min = $min;
			$numeric_max = $max;

			if ( $this->numberFormat == 'decimal_comma' ){
				$numeric_min = empty( $min ) ? '' : GFCommon::clean_number( $min, 'decimal_comma', '');
				$numeric_max = empty( $max ) ? '' : GFCommon::clean_number( $max, 'decimal_comma', '');
			}

			$message = '';

			if ( is_numeric( $numeric_min ) && is_numeric( $numeric_max ) ) {
				$message = sprintf( esc_html__( 'Please enter a number from %s to %s.', 'gravityforms' ), "<strong>$min</strong>", "<strong>$max</strong>" );
			} elseif ( is_numeric( $numeric_min ) ) {
				$message = sprintf( esc_html__( 'Please enter a number greater than or equal to %s.', 'gravityforms' ), "<strong>$min</strong>" );
			} elseif ( is_numeric( $numeric_max ) ) {
				$message = sprintf( esc_html__( 'Please enter a number less than or equal to %s.', 'gravityforms' ), "<strong>$max</strong>" );
			} elseif ( $this->failed_validation && $this->isRequired ) {
				$message = ''; // Required validation will take care of adding the message here.
			} elseif ( $this->failed_validation ) {
				$message = esc_html__( 'Please enter a valid number.', 'gravityforms' );
			}

			return $message;
		}

		public function is_value_submission_array() {
			return true;
		}
 
		public function get_field_input( $form, $value = '', $entry = null ) {
			$is_entry_detail = $this->is_entry_detail();
			$is_form_editor  = $this->is_form_editor();
			$is_admin        = $is_entry_detail || $is_form_editor;

			$is_entry_detail = $this->is_entry_detail();
			$is_form_editor  = $this->is_form_editor();

			$form_id  = $form['id'];
			$id       = intval( $this->id );
			$field_id = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";

			$size          = $this->size;
			$disabled_text = $is_form_editor ? "disabled='disabled'" : '';
			$class_suffix  = $is_entry_detail ? '_admin' : '';
			$class         = $size . $class_suffix;
			$class         = esc_attr( $class );

			$instruction = '';
			$read_only   = '';

			if ( ! $is_entry_detail && ! $is_form_editor ) {

				if ( $this->has_calculation() ) {

					// calculation-enabled fields should be read only
					$read_only = 'readonly="readonly"';

				} else {

					$message          = $this->get_range_message();
					$validation_class = $this->failed_validation ? 'validation_message' : '';

					if ( ! $this->failed_validation && ! empty( $message ) && empty( $this->errorMessage ) ) {
						$instruction = "<div class='instruction $validation_class' id='gfield_instruction_{$this->formId}_{$this->id}'>" . $message . '</div>';
					}
				}
			} elseif ( rgget('view') == 'entry' ) {
				$value = GFCommon::format_number( $value, $this->numberFormat, rgar( $entry, 'currency' ) );
			}

			$is_html5        = RGFormsModel::is_html5_enabled();
			$html_input_type = $is_html5 && ! $this->has_calculation() && ( $this->numberFormat != 'currency' && $this->numberFormat != 'decimal_comma' ) ? 'number' : 'text'; // chrome does not allow number fields to have commas, calculations and currency values display numbers with commas
			$step_attr       = $is_html5 ? "step='any'" : '';

			$min = $this->rangeMin;
			$max = $this->rangeMax;

			$min_attr = $is_html5 && is_numeric( $min ) ? "min='{$min}'" : '';
			$max_attr = $is_html5 && is_numeric( $max ) ? "max='{$max}'" : '';

			$include_thousands_sep = apply_filters( 'gform_include_thousands_sep_pre_format_number', $html_input_type == 'text', $this );
			$value                 = GFCommon::format_number( $value, $this->numberFormat, rgar( $entry, 'currency' ), $include_thousands_sep );

			$placeholder_attribute  = $this->get_field_placeholder_attribute();
			$required_attribute     = $this->isRequired ? 'aria-required="true"' : '';
			$invalid_attribute      = $this->failed_validation ? 'aria-invalid="true"' : 'aria-invalid="false"';

			$range_message          = $this->get_range_message();
			$describedby_extra_id   = empty( $range_message ) ? array() : array( "gfield_instruction_{$this->formId}_{$this->id}" );
			$aria_describedby       = $this->get_aria_describedby( $describedby_extra_id );

			$autocomplete_attribute = $this->enableAutocomplete ? $this->get_field_autocomplete_attribute() : '';

			$tabindex = $this->get_tabindex();

			// If we are in the form editor, display a placeholder field.
			// if ( $is_admin ) {
				$input = "<div class='ginput_container ginput_container_number'>";

				// the fields

				// the number / value
				$input .= sprintf( "<input name='input_%d' id='%s' type='{$html_input_type}' {$step_attr} {$min_attr} {$max_attr} value='%s' class='%s' {$tabindex} readonly='true' %s %s %s %s %s %s/>%s", $id, $field_id, esc_attr( $value ), esc_attr( $class ), $disabled_text, $placeholder_attribute, $required_attribute, $invalid_attribute, $aria_describedby, $autocomplete_attribute, $instruction );

				$input .= '</div>';
			// }
			return $input;
		}

		private function translateValueArray($value) {
			if (empty($value)) {
				return [];
			}
			$table_value = [];
			$counter = 0;
			foreach ($this->choices as $course) {
				foreach ($this->delivery_days as $day) {
					$table_value[$course['text']][$day] = $value[$counter++];
				}
			}
			return $table_value;
		}
 
		public function get_value_save_entry($value, $form, $input_name, $lead_id, $lead) {
			if (empty($value)) {
				$value = '';
			} else {
				$table_value = $this->translateValueArray($value);
				$value = serialize($table_value);
			}
			return $value;
		}
 
		private function prettyListOutput($value) {
			$str = '<ul>';
			foreach ($value as $course => $days) {
				$week = '';
				foreach ($days as $day => $delivery_number) {
					if (!empty($delivery_number)) {
						$week .= '<li>' . $day . ': ' . $delivery_number . '</li>';
					}
				}
				// Only add week if there were any requests at all
				if (!empty($week)) {
					$str .= '<li><h3>' . $course . '</h3><ul class="days">' . $week . '</ul></li>';
				}
			}
			$str .= '</ul>';
			return $str;
		}
 
		public function get_value_entry_list($value, $entry, $field_id, $columns, $form) {
			return __('Enter details to see delivery details', 'wbgf2wpt');
		}
 
		public function get_value_entry_detail($value, $currency = '', $use_text = false, $format = 'html', $media = 'screen') {
			$value = maybe_unserialize($value);		
			if (empty($value)) {
				return $value;
			}
			$str = $this->prettyListOutput($value);
			return $str;
		}
 
		public function get_value_merge_tag($value, $input_id, $entry, $form, $modifier, $raw_value, $url_encode, $esc_html, $format, $nl2br) {
			return $this->prettyListOutput($value);
		}
 
		public function is_value_submission_empty($form_id) {
			$value = rgpost('input_' . $this->id);
			foreach ($value as $input) {
				if (strlen(trim($input)) > 0) {
					return false;
				}
			}
			return true;
		}
	}
	GF_Fields::register(new WordPointsGF());
}

// admin notices
function wbgf2wpt_admin_notice__success() {
    $class = 'notice notice-success is-dismissible';
    $message = __( 'WordPoints for Gravity Forms activated.', 'gravity-forms-wordpoints' );
 
    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
}

function wbgf2wpt_admin_notice__error() {
    $class = 'notice notice-error';
    $message = __( 'WordPoints for Gravity Forms cannot be activated. Is your Gravity Forms plugin active?', 'gravity-forms-wordpoints' );
 
    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
}

 
/**
 * Hook into deactivation.
 *
 * @since 1.0.0
 */
function wbgf2wpt_deactivation() {
 
    /* Undo something here that you did on activation ... */
}
wordpoints_register_module_deactivation_hook( __FILE__, 'my_wordpoitns_extension_deactivation' );
/* Do other cool stuff */