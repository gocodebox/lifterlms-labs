<?php
/**
 * Simple branding.
 *
 * @package LifterLMS_Labs/Labs/Classes
 *
 * @since 1.2.0
 * @version 1.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Simple branding class.
 *
 * Customize all of those ugly ugly colors predefined by LifterLMS
 * with three easy color pickers.
 *
 * @since 1.0.0
 */
class LLMS_Lab_Simple_Branding extends LLMS_Lab {

	/**
	 * Configure the Lab.
	 *
	 * @since 1.0.0
	 * @since 1.7.0 Escaped strings.
	 *
	 * @return void
	 */
	protected function configure() {
		$this->id          = 'simple-branding';
		$this->title       = esc_html__( 'Simple Branding', 'lifterlms-labs' );
		$this->description = sprintf(
			// Translators: %1$s = Opening anchor tag; %2$s = Closing anchor tag.
			esc_html__( 'Customize the default colors of various LifterLMS elements. For help and more information click %1$shere%2$s.', 'lifterlms-labs' ),
			'<a href="https://lifterlms.com/docs/simple-branding-lab?utm_source=settings&utm_campaign=lifterlmslabsplugin&utm_medium=product&utm_content=simplebranding" target="blank">',
			'</a>'
		);
	}

	/**
	 * Initialize the Lab.
	 *
	 * @since 1.0.0
	 * @since 1.2.2 Unknown.
	 *
	 * @return void
	 */
	protected function init() {

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
		add_action( 'admin_footer', array( $this, 'admin_footer' ) );
		add_action( 'wp_head', array( $this, 'output_css' ), 777 );
		add_action( 'llms_lab_' . $this->id . '_settings_saved', array( $this, 'generate_css' ) );
		add_filter( 'llms_email_css', array( $this, 'email_css' ), 777, 1 );

	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function admin_enqueue() {
		wp_enqueue_script( 'iris' );
	}

	/**
	 * Rudimentary JS so we don't have to deal with a whole JS file for these few lines.
	 *
	 * @since 1.0.0
	 *
	 * @return void
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
	 * Adjust the given hexcode to be lighter or darker.
	 *
	 * @since 1.0.0
	 *
	 * @source https://gist.github.com/stephenharris/5532899
	 *
	 * @param string $hex     Color as hexadecimal (with or without hash).
	 * @param float  $percent Decimal ( 0.2 = lighten by 20%(), -0.4 = darken by 40%() ).
	 * @return string
	 */
	private function adjust_hex( $hex, $percent ) {

		$rgb     = $this->hex_to_rgb_array( $hex );
		$new_hex = '#';

		// Convert to decimal and change luminosity.
		foreach ( $rgb as $part ) {
			$dec      = min( max( 0, $part + $part * $percent ), 255 );
			$new_hex .= str_pad(
				dechex( (int) $dec ),
				2,
				0,
				STR_PAD_LEFT
			);
		}

		return $new_hex;

	}

	/**
	 * Add branding settings to LifterLMS emails CSS.
	 *
	 * @since 1.2.2
	 *
	 * @param array $css CSS rules from LLMS()->mailer().
	 * @return array
	 */
	public function email_css( $css ) {

		$primary      = $this->get_option( 'color_primary' );
		$primary_text = $this->get_luminance( $primary ) > 0.179 ? '#000' : '#fff';

		$action      = $this->get_option( 'color_action' );
		$action_text = $this->get_luminance( $action ) > 0.179 ? '#000' : '#fff';

		$css['button-background-color'] = $action;
		$css['button-font-color']       = $action_text;

		$css['heading-background-color'] = $primary;
		$css['heading-font-color']       = $primary_text;
		$css['main-color']               = $primary;

		return $css;

	}

	/**
	 * Called after this lab's settings are saved, generates a CSS block
	 * that can be output in the head and saves it to the options table
	 * so it doesn't have to be generated dynamically on every page load.
	 *
	 * @since 1.0.0
	 * @since 1.5.0 Unknown.
	 * @since 1.7.0 Escape what comes from the db.
	 *
	 * @return void
	 */
	public function generate_css() {

		$primary       = esc_attr( $this->get_option( 'color_primary', '#2295ff' ) );
		$primary_dark  = $this->adjust_hex( $primary, -0.12 );
		$primary_light = $this->adjust_hex( $primary, 0.08 );
		$primary_text  = $this->get_luminance( $primary ) > 0.179 ? '#000' : '#fff';

		$action       = esc_attr( $this->get_option( 'color_action', '#f8954f' ) );
		$action_dark  = $this->adjust_hex( $action, -0.12 );
		$action_light = $this->adjust_hex( $action, 0.08 );
		$action_text  = $this->get_luminance( $action ) > 0.179 ? '#000' : '#fff';

		$accent = esc_attr( $this->get_option( 'color_accent', '#ef476f' ) );

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

			/* instructors */
			.llms-instructor-info .llms-instructors .llms-author {
				border-top-color: <?php echo $primary; ?>;
			}

			.llms-instructor-info .llms-instructors .llms-author .avatar {
				background: <?php echo $primary; ?>;
				border-color: <?php echo $primary; ?>;
			}

			/* quizzes */
			.llms-question-wrapper ol.llms-question-choices li.llms-choice input:checked+.llms-marker {
				background: <?php echo $accent; ?>;
			}

			/* advanced quizzes */
			.llms-quiz-ui .llms-aq-scale .llms-aq-scale-range .llms-aq-scale-radio input[type="radio"]:checked + .llms-aq-scale-button {
				background: <?php echo $accent; ?>;
			}
			.llms-quiz-ui input.llms-aq-blank {
				color: <?php echo $primary; ?>;
			}
			.llms-quiz-ui input.llms-aq-blank:focus,
			.llms-quiz-ui input.llms-aq-blank:valid {
				border-bottom-color: <?php echo $primary; ?>;
			}
			.llms-quiz-ui .llms-aq-uploader.dragover {
				border-color: <?php echo $primary; ?>;
			}
			.llms-quiz-ui ol.llms-question-choices.llms-aq-reorder-list.dragging {
				box-shadow: 0 0 0 3px <?php echo $accent; ?>;
			}
			.llms-quiz-ui ol.llms-question-choices.llms-aq-reorder-list .llms-aq-reorder-item.llms-aq-placeholder {
				border-color: <?php echo $accent; ?>;
			}
			.llms-quiz-ui ol.llms-question-choices.llms-aq-reorder-list .llms-aq-reorder-item.llms-aq-placeholder:last-child {
				border-bottom-color: <?php echo $accent; ?>;
			}
		</style>
		<?php
		$css = ob_get_clean();
		// Remove comments.
		$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
		// Remove space after colons.
		$css = str_replace( ': ', ':', $css );
		// Remove whitespace.
		$css = str_replace( array( "\r\n", "\r", "\n", "\t" ), '', $css );
		$css = preg_replace( ' {2,}', ' ', $css );

		$this->set_option( 'css_string', $css );
	}

	/**
	 * Retrieve the relative luminance of a hex code
	 * used to determine what color should be used on top of the selected colors
	 * modified from the function and research and data found at http://stackoverflow.com/a/3943023/400568.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hex Hex code as a string.
	 * @return float
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
	 * Convert a hexcode to an rgba string that can be printed to a CSS rule.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hex   Hex code.
	 * @param float  $alpha Alpha vale.
	 * @return string
	 */
	private function hex_to_rgba( $hex, $alpha ) {
		$rgb = $this->hex_to_rgb_array( $hex );
		return 'rgba(' . implode( ',', $rgb ) . ',' . $alpha . ')';
	}

	/**
	 * Convert a hexcode string to an array with the RGB code for the string.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hex Hex code string with or without a leading "#".
	 * @return array Indexed array, 0=red, 1=green, 2=blue.
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
	 * Set defaults on lab enabling (if they're not already set) & generates the CSS string.
	 *
	 * Stub function called when lab is enabled.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public function on_enable() {

		$settings = array(
			'color_primary' => '#2295ff',
			'color_action'  => '#f8954f',
			'color_accent'  => '#ef476f',
		);

		foreach ( $settings as $setting => $val ) {
			if ( ! $this->get_option( $setting ) ) {
				$this->set_option( $setting, $val );
			}
		}

		$this->generate_css();

	}

	/**
	 * Output the generated css in the sites head on the frontend.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function output_css() {
		echo $this->get_option( 'css_string', '' );
	}

	/**
	 * Define the lab's settings.
	 *
	 * @since 1.0.0
	 * @since 1.4.0 Unknown.
	 * @since 1.7.0 Escape strings.
	 *
	 * @return array
	 */
	protected function settings() {
		return array(
			array(
				'columns'         => 10,
				'classes'         => 'llms-labs-colorpicker',
				'default'         => '#2295ff',
				'description'     => '<br>' . esc_html__( 'This primary color is used for mark as complete and continue buttons, borders on the pricing tables and checkout screen, and more.', 'lifterlms-labs' ),
				'id'              => 'llms-lab-branding-primary-color',
				'label'           => esc_html__( 'Primary Color', 'lifterlms-labs' ) . '<br>',
				'last_column'     => true,
				'name'            => 'color_primary',
				'required'        => true,
				'style'           => 'max-width:140px;',
				'type'            => 'text',
				'value'           => $this->get_option( 'color_primary' ),
				'wrapper_classes' => 'llms-labs-colorpicker-wrapper',
			),
			array(
				'columns'         => 10,
				'classes'         => 'llms-labs-colorpicker',
				'default'         => '#f8954f',
				'description'     => '<br>' . esc_html__( 'This color is used to draw focus to important actions like the buy now and enroll buttons.', 'lifterlms-labs' ),
				'id'              => 'llms-lab-branding-action-color',
				'label'           => esc_html__( 'Action Color', 'lifterlms-labs' ) . '<br>',
				'last_column'     => true,
				'name'            => 'color_action',
				'required'        => true,
				'style'           => 'max-width:140px;',
				'type'            => 'text',
				'value'           => $this->get_option( 'color_action' ),
				'wrapper_classes' => 'llms-labs-colorpicker-wrapper',
			),
			array(
				'columns'         => 10,
				'classes'         => 'llms-labs-colorpicker',
				'default'         => '#ef476f',
				'description'     => '<br>' . esc_html__( 'This color is used for minor accents like progress bars and icons.', 'lifterlms-labs' ),
				'id'              => 'llms-lab-branding-accent-color',
				'label'           => esc_html__( 'Accent Color', 'lifterlms-labs' ) . '<br>',
				'last_column'     => true,
				'name'            => 'color_accent',
				'required'        => true,
				'style'           => 'max-width:140px;',
				'type'            => 'text',
				'value'           => $this->get_option( 'color_accent' ),
				'wrapper_classes' => 'llms-labs-colorpicker-wrapper',
			),
		);
	}

}

return new LLMS_Lab_Simple_Branding();
