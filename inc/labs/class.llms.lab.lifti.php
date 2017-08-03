<?php
/**
 * Lab: Lifti
 * Divi theme compatibility
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

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
	 * @version  1.2.0
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
			add_meta_box( 'et_settings_meta_box', sprintf(__('Divi %s Settings', 'Divi'), $obj->labels->singular_name ), 'et_single_settings_meta_box', $post_type, 'side', 'high' );

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
	 * @version  1.3.0
	 */
	public function admin_footer() {
		$screen = get_current_screen();
		if ( ! in_array( $screen->id, array( 'course', 'llms_membership' ) ) && ! $this->is_builder_enabled( $screen->id ) ) {
			return;
		}
		$msg = sprintf(
			__( 'This editor disabled when Divi Builder is active. %1$sLearn how%2$s to show different content to enrolled and non-enrolled students when using the Divi Builder.', 'lifterlms-labs' ),
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
	 * @version  1.2.0
	 */
	public function handle_content( $content ) {

		global $post;

		if ( ! $this->is_builder_enabled( $post ) ) {
			return $content;
		}

		$sections = $this->get_builder_sections( $content );


		if ( $sections ) {

			$restrictions = llms_page_restricted( $post->ID );

			$class = $restrictions['is_restricted'] ? 'llms-enrolled-student-content' : 'llms-non-enrolled-student-content';

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
	 * @version  1.2.0
	 */
	private function is_divi_enabled() {

		$theme = wp_get_theme();
		return ( 'Divi' === $theme->get_template() );

	}

	/**
	 * Create custom page builder predefined layout(s) when enabling the lab
	 * Stub function called when lab is enabled
	 * @return   void
	 * @since    1.2.0
	 * @version  1.2.0
	 */
	public function on_enable() {

		if ( ! $this->is_divi_enabled() ) {
			return;
		}

		$layouts = array(
			array(
				'name' => esc_html__( 'LifterLMS Course', 'lifterlms-labs' ),
				'content' => '[et_pb_section bb_built="1" admin_label="LifterLMS Enrolled Section" fullwidth="off" specialty="off" transparent_background="off" allow_player_pause="off" inner_shadow="off" parallax="off" parallax_method="on" make_fullwidth="off" use_custom_width="off" width_unit="on" make_equal="off" use_custom_gutter="off" module_class="llms-enrolled-student-content"][et_pb_row admin_label="Row"][et_pb_column type="2_3"][et_pb_text admin_label="Enrolled Student Text" background_layout="light" text_orientation="left" use_border_color="off" border_color="#ffffff" border_style="solid"]</p><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Respondeat totidem verbis. Bonum integritas corporis: misera debilitas. Duo Reges: constructio interrete. Hi curatione adhibita levantur in dies, valet alter plus cotidie, alter videt. Nos paucis ad haec additis finem faciamus aliquando; Faceres tu quidem, Torquate, haec omnia;</p><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Respondeat totidem verbis. Bonum integritas corporis: misera debilitas. Duo Reges: constructio interrete. Hi curatione adhibita levantur in dies, valet alter plus cotidie, alter videt. Nos paucis ad haec additis finem faciamus aliquando; Faceres tu quidem, Torquate, haec omnia;</p><p>[/et_pb_text][et_pb_text admin_label="Course Progress &amp; Continue Button" background_layout="light" text_orientation="left" use_border_color="off" border_color="#ffffff" border_style="solid"]</p><p>[lifterlms_course_continue]</p><p>[/et_pb_text][/et_pb_column][et_pb_column type="1_3"][et_pb_text admin_label="Course Outline" background_layout="light" text_orientation="left" use_border_color="off" border_color="#ffffff" border_style="solid"]</p><p><strong>Outline</strong></p><p>[lifterlms_course_outline collapse="true" toggles="true"] [/et_pb_text][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section bb_built="1" admin_label="LifterLMS Non-Enrolled Section" fullwidth="off" specialty="off" transparent_background="off" allow_player_pause="off" inner_shadow="off" parallax="off" parallax_method="on" make_fullwidth="off" use_custom_width="off" width_unit="on" make_equal="off" use_custom_gutter="off" module_class="llms-non-enrolled-student-content"][et_pb_row admin_label="Row"][et_pb_column type="2_3"][et_pb_text admin_label="Non-Enrolled Student Text" background_layout="light" text_orientation="left" use_border_color="off" border_color="#ffffff" border_style="solid"]</p><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Hoc sic expositum dissimile est superiori. Ut in voluptate sit, qui epuletur, in dolore, qui torqueatur. Idemne potest esse dies saepius, qui semel fuit? Expectoque quid ad id, quod quaerebam, respondeas. Duo Reges: constructio interrete. Sed quid attinet de rebus tam apertis plura requirere?</p><p>Quid enim possumus hoc agere divinius? Itaque hic ipse iam pridem est reiectus; Quid nunc honeste dicit? Hoc dixerit potius Ennius: Nimium boni est, cui nihil est mali.</p><p>Sint ista Graecorum; Uterque enim summo bono fruitur, id est voluptate. Qua tu etiam inprudens utebare non numquam. Tu autem, si tibi illa probabantur, cur non propriis verbis ea tenebas? Non est igitur voluptas bonum. Etenim semper illud extra est, quod arte comprehenditur. Quia dolori non voluptas contraria est, sed doloris privatio. Poterat autem inpune;</p><p>[/et_pb_text][/et_pb_column][et_pb_column type="1_3"][et_pb_text admin_label="Course Meta Information" background_layout="light" text_orientation="left" use_border_color="off" border_color="#ffffff" border_style="solid"]</p><p><strong>Course Details</strong></p><p>[lifterlms_course_meta_info] [/et_pb_text][et_pb_text admin_label="Course Author" background_layout="light" text_orientation="left" use_border_color="off" border_color="#ffffff" border_style="solid"] [lifterlms_course_author avatar_size="64" bio="yes"] [/et_pb_text][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section bb_built="1" admin_label="LifterLMS Content" transparent_background="off" allow_player_pause="off" inner_shadow="off" parallax="off" parallax_method="on" make_fullwidth="off" use_custom_width="off" width_unit="on" make_equal="off" use_custom_gutter="off"][et_pb_row admin_label="row"][et_pb_column type="4_4"][et_pb_text admin_label="Prerequisites Notice" background_layout="light" text_orientation="left" use_border_color="off" border_color="#ffffff" border_style="solid"] [lifterlms_course_prerequisites] [/et_pb_text][et_pb_text admin_label="Pricing Table" background_layout="light" text_orientation="left" use_border_color="off" border_color="#ffffff" border_style="solid"] [lifterlms_pricing_table] [/et_pb_text][et_pb_text admin_label="Course Syllabus" background_layout="light" text_orientation="left" use_border_color="off" border_color="#ffffff" border_style="solid"] [lifterlms_course_syllabus] [/et_pb_text][/et_pb_column][/et_pb_row][/et_pb_section]',
				'meta' => array(
					'_et_pb_predefined_layout'   => 'on',
					'_et_pb_built_for_post_type' => 'course',
				),
			),
		);

		foreach ( $layouts as $layout ) {

			// dupcheck
			$query = new WP_Query( array(
				'post_type' => ET_BUILDER_LAYOUT_POST_TYPE,
				'posts_per_page' => 1,
				'title' => $layout['name'],
				'meta_key' => '_et_pb_predefined_layout',
				'meta_value' => 'on',
			) );

			if ( ! $query->have_posts() ) {
				et_pb_create_layout( $layout['name'], $layout['content'], $layout['meta'] );
			}

		}

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
