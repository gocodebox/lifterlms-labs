<?php
/**
 * Lab: Lifti
 *
 * @package LifterLMS_Labs/Labs/Classes
 *
 * @since 1.1.0
 * @version 1.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Lab: Lifti.
 *
 * Divi theme compatibility.
 *
 * @since 1.1.0
 */
class LLMS_Lab_Lifti extends LLMS_Lab {

	/**
	 * Custom Post Types for the builder.
	 *
	 * @var array
	 */
	private $builder_cpts = array( 'course', 'lesson', 'llms_membership' );

	/**
	 * Custom Post Types for which the builder is enabled.
	 *
	 * @var array
	 */
	private $builder_cpts_enabled = array();

	/**
	 * Cache of the enrollment-based class to strip, keyed by post ID.
	 *
	 * @var array
	 */
	private $restricted_class_cache = array();

	/**
	 * Configure the Lab.
	 *
	 * @since 1.1.0
	 * @since 1.2.0 Unknown.
	 * @since 1.7.0 Escaped strings.
	 *
	 * @return void
	 */
	protected function configure() {

		$this->id = 'divi-friends'; // Leave this so we don't have to rewrite db options.
	}

	/**
	 * Initialize the Lab.
	 *
	 * @since 1.1.0
	 * @since 1.5.1 Unknown.
	 * @since 1.7.0 Replace use of deprecated `llms_get_quiz_theme_settings`.
	 *
	 * @return void
	 */
	protected function init() {

		if ( ! $this->is_divi_enabled() ) {
			return;
		}

		foreach ( $this->builder_cpts as $cpt ) {
			if ( 'yes' === $this->get_option( 'et_builder_' . $cpt ) ) {
				$this->builder_cpts_enabled[] = $cpt;
			}
		}

		add_action( 'lifterlms_before_main_content', array( $this, 'output_content_wrapper_start' ), 10 );
		add_action( 'lifterlms_after_main_content', array( $this, 'output_content_wrapper_end' ), 10 );

		add_action( 'init', array( $this, 'remove_llms_sidebars' ), 15 );
		add_action( 'admin_init', array( $this, 'include_template_functions' ) );

		add_filter( 'body_class', array( $this, 'body_class' ), 777 );

		// Enable the divi builder for lifterlms cpts.
		add_filter( 'et_builder_post_types', array( $this, 'builder_post_types' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
		add_action( 'admin_footer', array( $this, 'admin_footer' ), 777 );

		add_filter( 'the_content', array( $this, 'handle_content' ), 1 );
		add_filter( 'the_excerpt', array( $this, 'handle_excerpt' ), 777 );

		// Divi 5 stores layouts as blocks, so hook the per-module render filter instead of the shortcode-based content filter.
		// Use a priority later than 10 so this runs after Divi's own ConditionsRenderer::should_render(), which resets the
		// displayable value to true and would otherwise override our result.
		add_filter( 'divi_module_library_register_module_render_block', array( $this, 'maybe_hide_d5_module' ), 20, 3 );

		add_action( 'add_meta_boxes', array( $this, 'add_page_settings' ) );

		add_filter( 'llms_builder_register_custom_fields', array( $this, 'add_builder_quiz_settings' ) );
	}

	public function set_title_and_description() {
		$this->title       = esc_html__( 'Lifti: Divi Theme Compatibility', 'lifterlms-labs' );
		$this->description = sprintf(
		// Translators: %1$s = Opening anchor tag; %2$s = Closing anchor tag.
			esc_html__( 'Enable LifterLMS compatibility with the Divi Theme and Page Builder. For more information click %1$shere%2$s.', 'lifterlms-labs' ),
			'<a href="https://lifterlms.com/docs/lab-lifti/?utm_source=settings&utm_medium=product&utm_campaign=lifterlmslabsplugin&utm_content=lifti">',
			'</a>'
		);
	}

	/**
	 * Add Divi page settings to LifterLMS enabled post types.
	 *
	 * @return void
	 *
	 * @since 1.2.0
	 * @since 1.7.0 Escaped strings.
	 */
	public function add_page_settings() {

		if ( ! function_exists( 'et_single_settings_meta_box' ) ) {
			return;
		}

		foreach ( $this->builder_cpts_enabled as $post_type ) {

			$obj = get_post_type_object( $post_type );
			add_meta_box(
				'et_settings_meta_box',
				sprintf(
					// Translators: %s Is the singular post type name.
					esc_html__( 'Divi %s Settings', 'lifterlms-labs' ),
					$obj->labels->singular_name
				),
				'et_single_settings_meta_box',
				$post_type,
				'side',
				'high'
			);

		}
	}

	/**
	 * Enqueue admin scripts and styles.
	 *
	 * @since 1.2.0
	 * @since 1.7.0 Use strict comparison for `in_array`.
	 *
	 * @return void
	 */
	public function admin_enqueue() {

		$screen = get_current_screen();

		if ( in_array( $screen->id, array( 'course', 'llms_membership' ), true ) && $this->is_builder_enabled( $screen->id ) ) {

			// I think that the hidden editor Divi utilizes messes with the editor buttons and causes our custom WYSIWYG editors to
			// show without the associated css... maybe...
			wp_enqueue_style( 'editor-buttons' );

		}
	}

	/**
	 * Output some JS in the admin footer to handle toggling of the ET Builder.
	 *
	 * @since 1.2.0
	 * @since 1.5.2 Unknown.
	 * @since 1.7.0 Escaped strings. User `in_array` with strict type comparison.
	 *
	 * @return void
	 */
	public function admin_footer() {
		$screen = get_current_screen();
		if ( ! in_array( $screen->id, array( 'course', 'llms_membership' ), true ) && ! $this->is_builder_enabled( $screen->id ) ) {
			return;
		}
		$msg = sprintf(
			// Translators: %1$s = Opening anchor tag; %2$s = Closing anchor tag.
			esc_html__( 'This editor is disabled when the Divi Builder is active. Use a Builder-enabled page and the "Redirect to WordPress Page" option to build a sales page or %1$slearn how%2$s to show different content to enrolled and non-enrolled students when using the Divi Builder.', 'lifterlms-labs' ),
			'<a href="#">',
			'</a>'
		);
		?>
		<script type="text/javascript">
		;( function( $ ) {

			/**
			 * Determine if the et builder is currently enabled.
			 *
			 * @since Unknown.
			 *
			 * @return bool
			 */
			function is_builder_enabled() {
				return $( '#et_pb_toggle_builder' ).hasClass( 'et_pb_builder_is_used' );
			}

			/**
			 * Toggle the visibility of the default editors based on the status of the et builder.
			 *
			 * @since Unknown.
			 *
			 * @return void
			 */
			function toggle_editors( status ) {
				$eds = $( '#wp-content-wrap, #wp-excerpt-wrap' );
				if ( 'hide' === status ) {
					$eds.each( function() {
						$( this ).closest( '.llms-mb-list' ).append(  '<p class="llms-labs-lifti-msg"><?php echo $msg; ?></p>' );
					} );
					$eds.hide();
				} else {
					$eds.show();
					$( '.llms-labs-lifti-msg' ).remove();
				}
			}

			// When enabling the et builder, hide default editors.
			$( '#et_pb_toggle_builder' ).on( 'click', function() {
				if ( ! is_builder_enabled() ) {
					toggle_editors( 'hide' );
				}
			} );

			// When disabling the et builder, show default editors.
			$( 'body' ).on( 'click', '[data-action="deactivate_builder"] .et_pb_prompt_proceed', function() {
				toggle_editors( 'show' );
			} );

			var initial_display = is_builder_enabled() ? 'hide' : 'show';
			toggle_editors( initial_display );

		} )( jQuery );
		</script>
		<?php
	}

	/**
	 * Remove sidebar classes from the body and add the full-width class.
	 *
	 * @since 1.1.0
	 * @since 1.7.0 Use strict comparison for `array_search`.
	 *
	 * @param array $classes Array of body css classes.
	 * @return array
	 */
	public function body_class( $classes ) {

		if ( is_courses() || is_memberships() ) {

			// Remove all layouts.
			foreach ( array( 'et_right_sidebar', 'et_left_sidebar', 'et_full_width_page' ) as $class ) {
				$key = array_search( $class, $classes, true );
				if ( false !== $key ) {
					unset( $key );
				}
			}

			// Add the layout we want / settings with default to full width.
			$classes[] = 'et_full_width_page';

		}

		return $classes;
	}

	/**
	 * Add our custom post types to the array of post types the ET builder is enabled on.
	 *
	 * @since 1.2.0
	 *
	 * @param array $post_types Array of default post types.
	 * @return array
	 */
	public function builder_post_types( $post_types ) {
		return array_merge( $post_types, $this->builder_cpts_enabled );
	}

	/**
	 * Remove Builder Sections from post content if section class doesn't match current user's enrollment.
	 *
	 * @since 1.2.0
	 * @since 1.5.1 Unknown.
	 *
	 * @param string $content Post content.
	 * @return string
	 */
	public function handle_content( $content ) {

		global $post;

		if ( ! $this->is_builder_enabled( $post ) ) {
			return $content;
		}

		// Divi 5 renders content as blocks via do_blocks() and enrollment filtering is handled in
		// maybe_hide_d5_module(). Bail before the shortcode/wpautop flow, which would mangle block
		// markup and inject stray <p></p> tags.
		if ( $this->is_divi_5_content( $post, $content ) ) {
			return $content;
		}

		$sections = $this->get_builder_sections( $content );

		if ( $sections ) {

			$class = $this->get_restricted_class( $post );

			$new_content = '';
			foreach ( $sections as $section ) {
				if ( false === strpos( $section, $class ) ) {
					$new_content .= $section;
				}
			}
			$content = $new_content;

		}

		return wpautop( $content );
	}

	/**
	 * Remove Builder Sections from post excerpt if section class doesn't match current user's enrollment.
	 *
	 * @since 1.2.0
	 * @since 1.2.1 Unknown.
	 *
	 * @param string $excerpt Post excerpt.
	 * @return string
	 */
	public function handle_excerpt( $excerpt ) {

		global $post;

		if ( 'lesson' === $post->post_type || ! $this->is_builder_enabled( $post ) || $this->is_divi_5_content( $post, $post->post_content ) ) {
			return $excerpt;
		}

		return $this->handle_content( $post->post_content );
	}

	/**
	 * Parse post content into an array of page builder sections.
	 *
	 * @since 1.2.0
	 *
	 * @param string $content Post content.
	 * @return array|false
	 */
	private function get_builder_sections( $content ) {

		$content_parts = explode( '[et_pb_section', $content );
		$matches       = array();
		preg_match_all( '/\[et_pb_section.*?\]([^`]*?)\[\/et_pb_section\]/', $content, $matches );

		if ( $matches && isset( $matches[0] ) ) {
			return $matches[0];
		}

		return false;
	}

	/**
	 * Determine the enrollment-based CSS class that should be stripped for the current user.
	 *
	 * When a user is restricted from (not enrolled in) the post, sections flagged for enrolled
	 * students are removed. Otherwise, sections flagged for non-enrolled students are removed.
	 *
	 * @since 1.9.0
	 *
	 * @param WP_Post $post Post object.
	 * @return string The CSS class to strip: `llms-enrolled-student-content` or `llms-non-enrolled-student-content`.
	 */
	private function get_restricted_class( $post ) {

		if ( isset( $this->restricted_class_cache[ $post->ID ] ) ) {
			return $this->restricted_class_cache[ $post->ID ];
		}

		if ( 'lesson' === $post->post_type && 'yes' === get_post_meta( $post->ID, '_llms_free_lesson', true ) ) {

			$restricted = llms_is_user_enrolled( get_current_user_id(), $post->ID ) ? false : true;

		} else {

			$restrictions = llms_page_restricted( $post->ID );
			$restricted   = $restrictions['is_restricted'];

		}

		$class = $restricted ? 'llms-enrolled-student-content' : 'llms-non-enrolled-student-content';

		$this->restricted_class_cache[ $post->ID ] = $class;

		return $class;
	}

	/**
	 * Determine whether content is built with the Divi 5 (block-based) builder.
	 *
	 * Checks the `_et_pb_use_divi_5` post meta (set on Divi 5 Visual Builder saves) and, as a
	 * fallback, sniffs the content for Divi block markup since that meta is not set by every
	 * Divi 5 builder activation path.
	 *
	 * @since 1.9.0
	 *
	 * @param WP_Post|mixed $post    Post object (or other value, e.g. from get_queried_object()).
	 * @param string        $content Content to inspect.
	 * @return bool
	 */
	private function is_divi_5_content( $post, $content ) {

		if ( $post instanceof WP_Post && 'on' === get_post_meta( $post->ID, '_et_pb_use_divi_5', true ) ) {
			return true;
		}

		return is_string( $content ) && false !== strpos( $content, '<!-- wp:divi/' );
	}

	/**
	 * Collect the user-entered CSS classes from a Divi 5 module's attributes.
	 *
	 * A class can be added in two different places in the Divi 5 editor, stored in different
	 * attribute groups:
	 *
	 * - Advanced > CSS ID & Classes > "CSS Class" field: `module.advanced.htmlAttributes.{device}.value.class`.
	 * - Advanced > Attributes (custom HTML attributes), adding a `class` attribute:
	 *   `module.decoration.attributes.{device}.value.attributes[]` (a list of `name`/`value` pairs).
	 *
	 * @since 1.9.0
	 *
	 * @param array $attrs The module's merged attributes.
	 * @return string Space-separated list of CSS classes (may be empty).
	 */
	private function get_module_css_classes( $attrs ) {

		$classes = array();

		// "CSS Class" field. Collect across all breakpoints (desktop/tablet/phone).
		$html_attrs = isset( $attrs['module']['advanced']['htmlAttributes'] ) ? $attrs['module']['advanced']['htmlAttributes'] : array();
		foreach ( (array) $html_attrs as $breakpoint ) {
			if ( isset( $breakpoint['value']['class'] ) && is_string( $breakpoint['value']['class'] ) ) {
				$classes[] = $breakpoint['value']['class'];
			}
		}

		// Custom "Attributes" feature. Pull out any attribute named `class`.
		$custom_attrs = isset( $attrs['module']['decoration']['attributes'] ) ? $attrs['module']['decoration']['attributes'] : array();
		foreach ( (array) $custom_attrs as $key => $breakpoint ) {

			// Supports both the responsive (`{device}.value.attributes`) and flat (`attributes`) shapes.
			if ( 'attributes' === $key ) {
				$attributes_list = $breakpoint;
			} else {
				$attributes_list = isset( $breakpoint['value']['attributes'] ) ? $breakpoint['value']['attributes'] : array();
			}

			foreach ( (array) $attributes_list as $attribute ) {
				$attribute = (array) $attribute;
				if ( isset( $attribute['name'], $attribute['value'] ) && 'class' === $attribute['name'] ) {
					$classes[] = $attribute['value'];
				}
			}
		}

		return trim( implode( ' ', $classes ) );
	}

	/**
	 * Hide a Divi 5 module from output when its CSS class doesn't match the current user's enrollment.
	 *
	 * Divi 5 stores layouts as `wp:divi/*` blocks rendered via `do_blocks()`, so the shortcode-based
	 * `handle_content()` filter no longer applies. This hooks Divi 5's per-module render filter and
	 * suppresses any module flagged with the enrollment class that should be hidden for the current user.
	 *
	 * @since 1.9.0
	 *
	 * @param bool     $display Whether the module should be rendered.
	 * @param WP_Block $block   The block instance being rendered.
	 * @param array    $attrs   The module's merged attributes.
	 * @return bool
	 */
	public function maybe_hide_d5_module( $display, $block, $attrs ) {

		if ( ! $display ) {
			return $display;
		}

		$post = get_queried_object();

		// This filter only fires while Divi 5 is rendering its blocks, so no separate Divi 5 detection
		// is needed here. Limit to singular LifterLMS builder-enabled posts.
		if ( ! is_singular() || ! $this->is_builder_enabled( $post ) ) {
			return $display;
		}

		$css = $this->get_module_css_classes( $attrs );

		if ( '' === $css ) {
			return $display;
		}

		$class_to_remove = $this->get_restricted_class( $post );

		if ( in_array( $class_to_remove, preg_split( '/\s+/', $css ), true ) ) {
			return false;
		}

		return $display;
	}

	/**
	 * Include LifterLMS Template Functions on the admin panel so widgets and shortcodes can be used
	 * within the Divi builder.
	 *
	 * @since 1.1.2
	 *
	 * @return void
	 */
	public function include_template_functions() {
		include_once LLMS_PLUGIN_DIR . 'includes/llms.template.functions.php';
	}

	/**
	 * Determine if the ET Builder is enabled for a post type.
	 *
	 * @since 1.2.0
	 * @since 1.7.0 Use strict comparison for `in_array`.
	 *
	 * @param string $post_or_post_type Post instance or post type name.
	 * @return bool
	 */
	public function is_builder_enabled( $post_or_post_type ) {

		if ( is_a( $post_or_post_type, 'WP_Post' ) ) {
			$post      = $post_or_post_type;
			$post_type = $post->post_type;
			$meta      = ( 'on' === get_post_meta( $post->ID, '_et_pb_use_builder', true ) );
		} else {
			$post_type = $post_or_post_type;
			$meta      = true;
		}

		$enabled = in_array( $post_type, $this->builder_cpts_enabled, true );

		return ( $enabled && $meta );
	}

	/**
	 * Determine if Divi is the current theme/template.
	 *
	 * @since 1.2.0
	 * @since 1.5.1 Unknown.
	 *
	 * @return bool
	 */
	private function is_divi_enabled() {

		// The template slug matches the theme directory name, which is `Divi` for a standard install.
		if ( 'divi' === strtolower( (string) get_template() ) ) {
			return true;
		}

		// The Divi 5 theme directory may be renamed (e.g. "Divi 5"). This method runs at plugin load,
		// before the theme (and Divi's functions/constants) is loaded, so fall back to the parent
		// theme's "Theme Name" header, which remains "Divi".
		$theme = wp_get_theme( get_template() );

		return $theme->exists() && 'divi' === strtolower( (string) $theme->get( 'Name' ) );
	}

	/**
	 * Output the opening Divi content wrapper tags.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function output_content_wrapper_start() {
		echo '
			<div id="main-content">
				<div class="container">
					<div id="content-area" class="clearfix">';
		echo '<div id="left-area">';
	}

	/**
	 * Output the closing Divi content wrapper tags.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function output_content_wrapper_end() {
		echo '</div><!-- #left-area -->';
		echo '
					</div> <!-- #content-area -->
				</div> <!-- .container -->
			</div> <!-- #main-content -->';
	}

	/**
	 * Add quiz sidebar layout compatibility options to Divi.
	 *
	 * @since 1.7.0
	 *
	 * @param array $builder_fields Builder fields.
	 * @return array
	 */
	public function add_builder_quiz_settings( $builder_fields ) {

		if ( ! isset( $builder_fields['quiz'] ) ) {
			$builder_fields['quiz'] = array();
		}

		$builder_fields['quiz']['layout'] = array(
			'title'      => esc_html__( 'Divi Theme Settings', 'lifterlms-labs' ),
			'toggleable' => true,
			'fields'     => array(
				array(
					array(
						'attribute'        => 'et_pb_page_layout',
						'attribute_prefix' => '_',
						'label'            => esc_html__( 'Layout', 'lifterlms-labs' ),
						'options'          => array(
							'et_full_width_page' => esc_html__( 'Fullwidth', 'lifterlms-labs' ),
							'et_left_sidebar'    => esc_html__( 'Left Sidebar', 'lifterlms-labs' ),
							'et_right_sidebar'   => esc_html__( 'Right Sidebar', 'lifterlms-labs' ),
						),
						'type'             => 'select',
					),
				),
			),
		);
		return $builder_fields;
	}

	/**
	 * Add quiz sidebar layout compatibility options to Divi.
	 *
	 * @since 1.5.1
	 * @since 1.7.0 Escaped strings.
	 *
	 * @deprecated 1.7.0
	 *
	 * @param array $settings Quiz settings array.
	 * @return array
	 */
	public function quiz_settings( $settings ) {

		llms_deprecated_function( __METHOD__, '1.7.0' );

		$settings['layout'] = array(
			'id'        => 'et_pb_page_layout',
			'id_prefix' => '_',
			'name'      => esc_html__( 'Layout', 'lifterlms-labs' ),
			'options'   => array(
				'et_full_width_page' => esc_html__( 'Fullwidth', 'lifterlms-labs' ),
				'et_left_sidebar'    => esc_html__( 'Left Sidebar', 'lifterlms-labs' ),
				'et_right_sidebar'   => esc_html__( 'Right Sidebar', 'lifterlms-labs' ),
			),
			'type'      => 'select',
		);

		return $settings;
	}

	/**
	 * Late initialization for removal of lifterlms sidebars.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function remove_llms_sidebars() {
		remove_action( 'lifterlms_sidebar', 'lifterlms_get_sidebar', 10 );
	}

	/**
	 * Get lab settings.
	 *
	 * @since 1.1.0
	 * @since 1.2.0 Unknown.
	 * @since 1.7.0 Escaped strings.
	 *
	 * @return array
	 */
	public function settings() {

		$settings = array(
			array(
				'type'  => 'html',
				'value' => '<strong>' . esc_html__( 'Enable Divi Builder & Layout Settings on the following LifterLMS Post Types', 'lifterlms-labs' ) . '</strong>',
			),
		);

		foreach ( array( 'course', 'lesson', 'llms_membership' ) as $cpt ) {
			$object     = get_post_type_object( $cpt );
			$settings[] = array(
				'columns'     => 12,
				'default'     => 'no',
				'id'          => 'llms-lab-divi-post-types-' . $cpt,
				'label'       => $object->label,
				'last_column' => true,
				'name'        => 'et_builder_' . $cpt,
				'required'    => false,
				'selected'    => ( 'yes' === $this->get_option( 'et_builder_' . $cpt ) ),
				'style'       => 'display:inline-block;margin-bottom:0;',
				'type'        => 'checkbox',
				'value'       => 'yes',
			);
		}

		return $settings;
	}
}

return new LLMS_Lab_Lifti();
