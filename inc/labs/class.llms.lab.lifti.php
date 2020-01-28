<?php
defined( 'ABSPATH' ) || exit;

/**
 * Lab: Lifti
 * Divi theme compatibility
 * @since    1.1.0
 * @version  1.5.2
 */
class LLMS_Lab_Lifti extends LLMS_Lab {

	private $builder_cpts = array( 'course', 'lesson', 'llms_membership' );
	private $builder_cpts_enabled = array();

	/**
	 * Configure the Lab
	 * @return   void
	 * @since    1.1.0
	 * @version  1.2.0
	 */
	protected function configure() {

		$this->id = 'divi-friends'; // leave this so we don't have to rewrite db options
		$this->title = __( 'Lifti: Divi Theme Compatibility', 'lifterlms-labs' );
		$this->description = sprintf(
			__( 'Enable LifterLMS compatibility with the Divi Theme and Page Builder. For more information click %1$shere%2$s.', 'lifterlms-labs' ),
			'<a href="https://lifterlms.com/docs/lab-lifti/?utm_source=settings&utm_medium=product&utm_campaign=lifterlmslabsplugin&utm_content=lifti">', '</a>'
		);

	}

	/**
	 * Initialize the Lab
	 * @return   void
	 * @since    1.1.0
	 * @version  1.5.1
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

		// enable the divi builder for lifterlms cpts
		add_filter( 'et_builder_post_types', array( $this, 'builder_post_types' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
		add_action( 'admin_footer', array( $this, 'admin_footer' ), 777 );

		add_filter( 'the_content', array( $this, 'handle_content' ), 1 );
		add_filter( 'the_excerpt', array( $this, 'handle_excerpt' ), 777 );

		add_action( 'add_meta_boxes', array( $this, 'add_page_settings' ) );

		if ( ! get_theme_support( 'lifterlms-quizzes' ) ) {
			add_theme_support( 'lifterlms-quizzes' );
			add_filter( 'llms_get_quiz_theme_settings', array( $this, 'quiz_settings' ) );
		}


	}

	/**
	 * Add Divi page settings to LifterLMS enabled post types
	 * @return   void
	 * @since    1.2.0
	 * @version  1.2.0
	 */
	public function add_page_settings() {

		if ( ! function_exists( 'et_single_settings_meta_box' ) ) {
			return;
		}

		foreach ( $this->builder_cpts_enabled as $post_type ) {

			$obj = get_post_type_object( $post_type );
			add_meta_box( 'et_settings_meta_box', sprintf(__('Divi %s Settings', 'lifterlms-labs'), $obj->labels->singular_name ), 'et_single_settings_meta_box', $post_type, 'side', 'high' );

		}

	}

	/**
	 * Enqueue admin scripts and styles
	 * @return   void
	 * @since    1.2.0
	 * @version  1.2.0
	 */
	public function admin_enqueue() {

		$screen = get_current_screen();

		if ( in_array( $screen->id, array( 'course', 'llms_membership' ) ) && $this->is_builder_enabled( $screen->id ) ) {

			// i think that the hidden editor Divi utilizes messes with the editor buttons and causes our custom WYSIWYG editors to
			// show without the associated css... maybe...
			wp_enqueue_style( 'editor-buttons' );

		}

	}

	/**
	 * Output some JS in the admin footer to handle toggling of the ET Builder
	 * @return   void
	 * @since    1.2.0
	 * @version  1.5.2
	 */
	public function admin_footer() {
		$screen = get_current_screen();
		if ( ! in_array( $screen->id, array( 'course', 'llms_membership' ) ) && ! $this->is_builder_enabled( $screen->id ) ) {
			return;
		}
		$msg = sprintf(
			__( 'This editor is disabled when the Divi Builder is active. Use a Builder-enabled page and the "Redirect to WordPress Page" option to build a sales page or %1$slearn how%2$s to show different content to enrolled and non-enrolled students when using the Divi Builder.', 'lifterlms-labs' ),
			'<a href="#">', '</a>'
		);
		?>
		<script type="text/javascript">
		;( function( $ ) {

			/**
			 * Determine if the et builder is currently enabled
			 * @return   bool
			 */
			function is_builder_enabled() {
				return $( '#et_pb_toggle_builder' ).hasClass( 'et_pb_builder_is_used' );
			}

			/**
			 * Toggle the visibilty of the default editors based on the status of the et builder
			 * @return   void
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

			// when enabling the et builder, hide default editors
			$( '#et_pb_toggle_builder' ).on( 'click', function() {
				if ( ! is_builder_enabled() ) {
					toggle_editors( 'hide' );
				}
			} );

			// when disabling the et builder, show default editors
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
	 * Remove sidebar classes from the body and add the full-width class
	 * @param    array     $classes  array of body css classes
	 * @return   array
	 * @since    1.1.0
	 * @version  1.1.0
	 */
	public function body_class( $classes ) {

		if ( is_courses() || is_memberships() ) {

			// remove all layouts
			foreach ( array( 'et_right_sidebar', 'et_left_sidebar', 'et_full_width_page' ) as $class ) {
				$key = array_search( $class, $classes );
				if ( false !== $key ) {
					unset( $key );
				}
			}

			// add the layout we want / settings with default to full width
			$classes[] = 'et_full_width_page';

		}

		return $classes;

	}

	/**
	 * Add our custom post types to the array of post types the ET builder is enabled on
	 * @param    array     $post_types  array of default post types
	 * @return   array
	 * @since    1.2.0
	 * @version  1.2.0
	 */
	public function builder_post_types( $post_types ) {
		return array_merge( $post_types, $this->builder_cpts_enabled );
	}

	/**
	 * Remove Builder Sections from post content if section class doesn't match current user's enrollment
	 * @param    string     $content  post content
	 * @return   string
	 * @since    1.2.0
	 * @version  1.5.1
	 */
	public function handle_content( $content ) {

		global $post;

		if ( ! $this->is_builder_enabled( $post ) ) {
			return $content;
		}

		$sections = $this->get_builder_sections( $content );

		if ( $sections ) {

			if ( 'lesson' === $post->post_type && 'yes' === get_post_meta( $post->ID, '_llms_free_lesson', true ) ) {

				$restricted = llms_is_user_enrolled( get_current_user_id(), $post->ID ) ? false : true;

			} else {

				$restrictions = llms_page_restricted( $post->ID );
				$restricted = $restrictions['is_restricted'];

			}

			$class = $restricted ? 'llms-enrolled-student-content' : 'llms-non-enrolled-student-content';

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
	 * Remove Builder Sections from post excerpt if section class doesn't match current user's enrollment
	 * @param    string     $excerpt  post excerpt
	 * @return   string
	 * @since    1.2.0
	 * @version  1.2.1
	 */
	public function handle_excerpt( $excerpt ) {

		global $post;

		if ( 'lesson' === $post->post_type || ! $this->is_builder_enabled( $post ) ) {
			return $excerpt;
		}

		return $this->handle_content( $post->post_content );

	}

	/**
	 * Parse post content into an array of page builder sections
	 * @param    string     $content  post content
	 * @return   array|false
	 * @since    1.2.0
	 * @version  1.2.0
	 */
	private function get_builder_sections( $content ) {

		$content_parts = explode( '[et_pb_section', $content );
		$matches = array();
		preg_match_all( '/\[et_pb_section.*?\]([^`]*?)\[\/et_pb_section\]/', $content, $matches );

		if ( $matches && isset( $matches[0] ) ) {
			return $matches[0];
		}

		return false;

	}

	/**
	 * Include LifterLMS Template Functions on the admin panel so widgets and shortcodes can be used
	 * within the Divi builder
	 * @return   void
	 * @since    1.1.2
	 * @version  1.1.2
	 */
	public function include_template_functions() {
		include_once LLMS_PLUGIN_DIR . 'includes/llms.template.functions.php' ;
	}

	/**
	 * Determine if the ET Builder is enabled for a post type
	 * @param    string     $post_type  post type name
	 * @return   boolean
	 * @since    1.2.0
	 * @version  1.2.0
	 */
	public function is_builder_enabled( $post_or_post_type ) {

		if ( is_a( $post_or_post_type, 'WP_Post' ) ) {
			$post = $post_or_post_type;
			$post_type = $post->post_type;
			$meta = ( 'on' === get_post_meta( $post->ID, '_et_pb_use_builder', true ) );
		} else {
			$post_type = $post_or_post_type;
			$meta = true;
		}

		$enabled = in_array( $post_type, $this->builder_cpts_enabled );

		return ( $enabled && $meta );

	}

	/**
	 * Determine if Divi is the current theme/template
	 * @return   boolean
	 * @since    1.2.0
	 * @version  1.5.1
	 */
	private function is_divi_enabled() {

		$theme = wp_get_theme();
		return ( 'divi' === strtolower( $theme->get_template() ) );

	}

	/**
	 * Output the opening Divi content wrapper tags
	 * @return   void
	 * @since    1.1.0
	 * @version  1.1.0
	 */
	public function output_content_wrapper_start() {
		echo '
			<div id="main-content">
				<div class="container">
					<div id="content-area" class="clearfix">';
		echo '<div id="left-area">';
	}

	/**
	 * Output the closing Divi content wrapper tags
	 * @return   void
	 * @since    1.1.0
	 * @version  1.1.0
	 */
	public function output_content_wrapper_end() {
		echo '</div><!-- #left-area -->';
		echo '
					</div> <!-- #content-area -->
				</div> <!-- .container -->
			</div> <!-- #main-content -->';
	}

	/**
	 * Add quiz sidebar layout compatibility options to Divi
	 * @param    array     $settings  quiz settings array
	 * @return   array
	 * @since    1.5.1
	 * @version  1.5.1
	 */
	public function quiz_settings( $settings ) {

		$settings['layout'] = array(
			'id' => 'et_pb_page_layout',
			'id_prefix' => '_',
			'name' => __( 'Layout', 'lifterlms-labs' ),
			'options' => array(
				'et_full_width_page' => esc_html__( 'Fullwidth', 'lifterlms-labs' ),
				'et_left_sidebar'    => esc_html__( 'Left Sidebar', 'lifterlms-labs' ),
				'et_right_sidebar'   => esc_html__( 'Right Sidebar', 'lifterlms-labs' ),
			),
			'type' => 'select',
		);

		return $settings;
	}

	/**
	 * Late initialization for removal of lifterlms sidebars
	 * @return    void
	 * @since     1.1.0
	 * @version   1.1.1
	 */
	public function remove_llms_sidebars() {
		remove_action( 'lifterlms_sidebar', 'lifterlms_get_sidebar', 10 );
	}

	/**
	 * Get lab settings
	 * @return   array
	 * @since    1.1.0
	 * @version  1.2.0
	 */
	public function settings() {

		$settings = array(
			array(
				'type' => 'html',
				'value' => '<strong>' . __( 'Enable Divi Builder & Layout Settings on the following LifterLMS Post Types', 'lifterlms-labs' ) . '</strong>',
			),
		);

		foreach ( array( 'course', 'lesson', 'llms_membership' ) as $cpt ) {
			$object = get_post_type_object( $cpt );
			$settings[] = array(
				'columns' => 12,
				'default' => 'no',
				'id' => 'llms-lab-divi-post-types-' . $cpt,
				'label' => $object->label,
				'last_column' => true,
				'name' => 'et_builder_' . $cpt,
				'required' => false,
				'selected' => ( 'yes' === $this->get_option( 'et_builder_' . $cpt ) ),
				'style' => 'display:inline-block;margin-bottom:0;',
				'type'  => 'checkbox',
				'value' => 'yes',
			);
		}

		return $settings;
	}

}

return new LLMS_Lab_Lifti();
