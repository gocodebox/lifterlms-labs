<?php
/**
 * Simple branding
 *
 * Customize all of those ugly ugly colors predefined by LifterLMS
 * with three easy color pickers
 *
 * If this isn't enough for you you should go learn some CSS
 * #kimdealwithit
 *
 * @since    1.0.0
 * @version  1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class LLMS_Lab_Simple_Branding extends LLMS_Lab {

	/**
	 * Configure the Lab
	 * @return   void
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	protected function configure() {
		$this->id = 'simple-branding';
		$this->title = __( 'Simple Branding', 'lifterlms-labs' );
		$this->description = sprintf( __( 'Customize the default colors of various LifterLMS elements. For help and more information click %1$shere%2$s.', 'lifterlms-labs' ), '<a href="https://lifterlms.com/docs/simple-branding-lab?utm_source=settings&utm_campaign=lifterlmslabsplugin&utm_medium=product&utm_content=simplebranding" target="blank">', '</a>' );
	}

	/**
	 * Initialize the Lab
	 * @return   void
	 * @since    1.0.0
	 * @version  1.2.2
	 */
	protected function init() {

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
		add_action( 'admin_footer', array( $this, 'admin_footer' ) );
		add_action( 'wp_head', array( $this, 'output_css' ), 777 );
		add_action( 'llms_lab_' . $this->id . '_settings_saved', array( $this, 'generate_css' ) );
		add_filter( 'llms_email_css', array( $this, 'email_css' ), 777, 1 );

	}

	/**
	 * Enqueue admin scripts
	 * @return   void
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function admin_enqueue() {
		wp_enqueue_script( 'iris' );
	}

	/**
	 * Rudimentry JS so we don't have to deal with a whole JS file for these few lines
	 * @return   void
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function admin_footer() {
		?>
		<script>
		( function( $ ) {
			$( document ).on( 'ready', function() {
				var $inputs = $( '.llms-labs-colorpicker' );
				$inputs.each( function() {
					var $box = $( '<span />' );
					$box.css( {
						background: $( this ).val() || '#fff',
						border: '1px solid #ddd',
						'box-shadow': 'inset 0 1px 2px rgba(0,0,0,.07)',
						display: 'inline-block',
						height: '19px',
						'margin-right': '5px',
						position: 'relative',
						top: '5px',
						width: '19px',
					} );
					$( this ).before( $box );
				} );
				$inputs.iris( {
					palettes: true,
					change: function(event, ui) {
						var $box = $( '#' + event.target.id ).prev( 'span' );
						$box.css( 'background', ui.color.toString() );
						// event = standard jQuery event, produced by whichever control was changed.
						// ui = standard jQuery UI object, with a color member containing a Color.js object

						// change the headline color
						// $("#headlinethatchanges").css( 'color', ui.color.toString());
					}
				} );
				$inputs.on( 'focus', function() {
					$( this ).iris( 'show' );
				} );
				$inputs.on( 'blur', function() {
					var $el = $( this );
					setTimeout(function() {
						if ( ! $( document.activeElement ).closest( '.iris-picker' ).length ) {
							$el.iris('hide');
						} else {
							$el.focus();
						}
					}, 0);
				} );
			} );
		} )( jQuery );
		</script>
		<?php
	}

	/**
	 * Adjust the given hexcode to be lighter or darker
	 * @param    string   $hex       Color as hexadecimal (with or without hash);
	 * @param    float    $percent   Decimal ( 0.2 = lighten by 20%(), -0.4 = darken by 40%() )
	 * @return   string
	 * @source   https://gist.github.com/stephenharris/5532899
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	private function adjust_hex( $hex, $percent ) {

		$rgb = $this->hex_to_rgb_array( $hex );
		$new_hex = '#';

		// convert to decimal and change luminosity
		foreach( $rgb as $part ) {
			$dec = min( max( 0, $part + $part * $percent ), 255 );
			$new_hex .= str_pad( dechex( $dec ) , 2, 0, STR_PAD_LEFT );
		}

		return $new_hex;

	}

	/**
	 * Add branding settings to LifterLMS emails CSS
	 * @param    array     $css  CSS rules from LLMS()->mailer()
	 * @return   array
	 * @since    1.2.2
	 * @version  1.2.2
	 */
	public function email_css( $css ) {

		$primary = $this->get_option( 'color_primary' );
		$primary_text = $this->get_luminance( $primary ) > 0.179 ? '#000' : '#fff';

		$action = $this->get_option( 'color_action' );
		$action_text = $this->get_luminance( $action ) > 0.179 ? '#000' : '#fff';

		$css['button-background-color'] = $action;
		$css['button-font-color'] = $action_text;

		$css['heading-background-color'] = $primary;
		$css['heading-font-color'] = $primary_text;
		$css['main-color'] = $primary;

		return $css;

	}

	/**
	 * Called after this lab's settings are saved, generates a CSS block
	 * that can be output in the head and saves it to the options table
	 * so it doesn't have to be generated dynamically on every page load
	 * @return   void
	 * @since    1.0.0
	 * @version  1.4.0
	 */
	public function generate_css() {

		$primary = $this->get_option( 'color_primary', '#2295ff' );
		$primary_dark = $this->adjust_hex( $primary, -0.12 );
		$primary_light = $this->adjust_hex( $primary, 0.08 );
		$primary_text = $this->get_luminance( $primary ) > 0.179 ? '#000' : '#fff';

		$action = $this->get_option( 'color_action', '#f8954f' );
		$action_dark = $this->adjust_hex( $action, -0.12 );
		$action_light = $this->adjust_hex( $action, 0.08 );
		$action_text = $this->get_luminance( $action ) > 0.179 ? '#000' : '#fff';

		$accent = $this->get_option( 'color_accent', '#ef476f' );

		ob_start();
		?>
		<style id="llms-labs-<?php echo $this->id; ?>-css" type="text/css">
			/* primary buttons */
			.llms-button-primary {
				background: <?php echo $primary; ?>;
				color: <?php echo $primary_text; ?>;
			}
			.llms-button-primary:hover,
			.llms-button-primary.clicked {
				background: <?php echo $primary_dark; ?>;
				color: <?php echo $primary_text; ?>;
			}
			.llms-button-primary:focus,
			.llms-button-primary:active {
				background: <?php echo $primary_light; ?>;
				color: <?php echo $primary_text; ?>;
			}

			/* action buttons */
			.llms-button-action {
				background: <?php echo $action; ?>;
				color: <?php echo $action_text; ?>;
			}
			.llms-button-action:hover,
			.llms-button-action.clicked {
				background: <?php echo $action_dark; ?>;
				color: <?php echo $action_text; ?>;
			}
			.llms-button-action:focus,
			.llms-button-action:active {
				background: <?php echo $action_light; ?>;
				color: <?php echo $action_text; ?>;
			}

			/* pricing tables */
			.llms-access-plan-title,
			.llms-access-plan .stamp {
				background: <?php echo $primary; ?>;
				color: <?php echo $primary_text; ?>;
			}
			.llms-access-plan.featured .llms-access-plan-featured {
				background: <?php echo $primary_light; ?>;
				color: <?php echo $primary_text; ?>;
			}
			.llms-access-plan.featured .llms-access-plan-content,
			.llms-access-plan.featured .llms-access-plan-footer {
				border-left-color: <?php echo $primary; ?>;
				border-right-color: <?php echo $primary; ?>;
			}
			.llms-access-plan.featured .llms-access-plan-footer {
				border-bottom-color: <?php echo $primary; ?>;
			}
			.llms-access-plan-restrictions a {
				color: <?php echo $action; ?>;
			}
			.llms-access-plan-restrictions a:hover {
				color: <?php echo $action_dark; ?>;
			}

			/* checkout */
			.llms-checkout-wrapper .llms-form-heading {
				background: <?php echo $primary; ?>;
				color: <?php echo $primary_text; ?>;
			}
			.llms-checkout-section,
			.llms-checkout-wrapper form.llms-login {
				border-color: <?php echo $primary; ?>;
			}
			.llms-form-field.type-radio input[type=radio]:checked+label:before {
				background-image: -webkit-radial-gradient(center,ellipse,<?php echo $primary; ?> 0,<?php echo $primary; ?> 40%,#fafafa 45%);
				background-image: radial-gradient(ellipse at center,<?php echo $primary; ?> 0,<?php echo $primary; ?> 40%,#fafafa 45%);
			}

			/* notices */
			.llms-notice {
				border-color: <?php echo $primary; ?>;
				background: <?php echo $this->hex_to_rgba( $primary, 0.3 ); ?>;
			}

			/* notifications */
			.llms-notification {
				border-top-color: <?php echo $primary; ?>;
			}

			/* progress bar */
			.llms-progress .progress-bar-complete {
				background-color: <?php echo $accent; ?>;
			}

			/* icons */
			.llms-widget-syllabus .lesson-complete-placeholder.done,
			.llms-widget-syllabus .llms-lesson-complete.done,
			.llms-lesson-preview.is-complete .llms-lesson-complete,
			.llms-lesson-preview.is-free .llms-lesson-complete {
				color: <?php echo $accent; ?>;
			}
			.llms-lesson-preview .llms-icon-free {
				background: <?php echo $accent; ?>;
			}
		</style>
		<?php
		$css = ob_get_clean();
		// Remove comments
		$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
		// Remove space after colons
		$css = str_replace(': ', ':', $css);
		// Remove whitespace
		$css = str_replace(array("\r\n", "\r", "\n", "\t"), '', $css);
		$css = preg_replace(" {2,}", ' ',$css);

		$this->set_option( 'css_string', $css );
	}

	/**
	 * Retrieve the relative luminance of a hex code
	 * used to determine what color should be used on top of the selected colors
	 * modified from the function and reserach and data found at http://stackoverflow.com/a/3943023/400568
	 * @param    string     $hex  hex code as a string
	 * @return   float
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	private function get_luminance( $hex ) {

		$rgb = $this->hex_to_rgb_array( $hex );

		foreach ( $rgb as &$c ) {
			$c = $c / 255;
			if ( $c <= 0.03928 ) {
				$c = $c / 12.92;
			} else {
				$c = pow( ( ( $c + 0.055 ) / 1.055 ), 2.4 );
			}
		}

		return ( 0.2126 * $rgb[0] ) + ( 0.7152 * $rgb[1] ) + ( 0.0722 * $rgb[2] );

	}

	/**
	 * Convert a hexcode to an rgba string that can be printed to a CSS rule
	 * @param    string     $hex    hex code
	 * @param    float     $alpha  alpha vale
	 * @return   string
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	private function hex_to_rgba( $hex, $alpha ) {
		$rgb = $this->hex_to_rgb_array( $hex );
		return 'rgba(' . implode( ',', $rgb ) . ',' . $alpha . ')';
	}

	/**
	 * Convert a hexcode string to an array with the RGB code for the string
	 * @param    string     $hex  hex code string with or without a leading "#"
	 * @return   array            indexed array, 0=red, 1=green, 2=blue
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	private function hex_to_rgb_array( $hex ) {

		$hex = preg_replace( '/[^0-9a-f]/i', '', $hex );

		if ( strlen( $hex ) < 6 ) {
			$hex = $hex[0] + $hex[0] + $hex[1] + $hex[1] + $hex[2] + $hex[2];
		}

		$rgb = array(
			hexdec( $hex[0] . $hex[1] ),
			hexdec( $hex[2] . $hex[3] ),
			hexdec( $hex[4] . $hex[5] ),
		);

		return $rgb;

	}

	/**
	 * Output the generated css in the sites head on the frontend
	 * @return   void
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function output_css() {
		echo $this->get_option( 'css_string', '' );
	}

	/**
	 * Define the lab's settings
	 * @return   array
	 * @since    1.0.0
	 * @version  1.4.0
	 */
	protected function settings() {
		return array(
			array(
				'columns' => 10,
				'classes' => 'llms-labs-colorpicker',
				'default' => '#2295ff',
				'description' => '<br>' . __( 'This primary color is used for mark as complete and continue buttons, borders on the pricing tables and checkout screen, and more.', 'lifterlms' ),
				'id' => 'llms-lab-branding-primary-color',
				'label' => __( 'Primary Color', 'lifterlms-labs' ) . '<br>',
				'last_column' => true,
				'name' => 'color_primary',
				'required' => true,
				'style' => 'max-width:140px;',
				'type'  => 'text',
				'value' => $this->get_option( 'color_primary' ),
				'wrapper_classes' => 'llms-labs-colorpicker-wrapper',
			),
			array(
				'columns' => 10,
				'classes' => 'llms-labs-colorpicker',
				'default' => '#f8954f',
				'description' => '<br>' . __( 'This color is used to draw focus to important actions like the buy now and enroll buttons.', 'lifterlms' ),
				'id' => 'llms-lab-branding-action-color',
				'label' => __( 'Action Color', 'lifterlms-labs' ) . '<br>',
				'last_column' => true,
				'name' => 'color_action',
				'required' => true,
				'style' => 'max-width:140px;',
				'type'  => 'text',
				'value' => $this->get_option( 'color_action' ),
				'wrapper_classes' => 'llms-labs-colorpicker-wrapper',
			),
			array(
				'columns' => 10,
				'classes' => 'llms-labs-colorpicker',
				'default' => '#ef476f',
				'description' => '<br>' . __( 'This color is used for minor accents like progress bars and icons.', 'lifterlms' ),
				'id' => 'llms-lab-branding-accent-color',
				'label' => __( 'Accent Color', 'lifterlms-labs' ) . '<br>',
				'last_column' => true,
				'name' => 'color_accent',
				'required' => true,
				'style' => 'max-width:140px;',
				'type'  => 'text',
				'value' => $this->get_option( 'color_accent' ),
				'wrapper_classes' => 'llms-labs-colorpicker-wrapper',
			),
		);
	}

}

return new LLMS_Lab_Simple_Branding;
