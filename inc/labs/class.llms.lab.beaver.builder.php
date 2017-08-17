<?php
/**
 * BeaverBuilder Integration
 *
 * Lets you do all them sweet BeaverBuilder things to Courses, Lessons, and Memberships
 *
 * @since    1.3.0
 * @version  1.3.1
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class LLMS_Lab_Beaver_Builder extends LLMS_Lab {

	/**
	 * Configure the Lab
	 * @return   void
	 * @since    1.3.0
	 * @version  1.3.0
	 */
	protected function configure() {

		define( 'LLMS_LABS_BB_MODULES_DIR', plugin_dir_path( __FILE__ ) . 'inc/beaver-builder/modules/' );
		define( 'LLMS_LABS_BB_MODULES_URL', plugins_url( '/', __FILE__ ) . 'inc/beaver-builder/modules/' );

		$this->id = 'beaver-builder';
		$this->title = __( 'Beaver Builder', 'lifterlms-labs' );
		$this->description = sprintf( __( 'Adds LifterLMS elements as pagebuilder modules and enables row and module visibility settings based on student enrollment in courses and memberships. For help and more information click %1$shere%2$s.', 'lifterlms-labs' ), '<a href="https://lifterlms.com/docs/lab-beaver-builder?utm_source=settings&utm_campaign=lifterlmslabsplugin&utm_medium=product&utm_content=beaverbuilder" target="blank">', '</a>' );

	}

	/**
	 * Determine if the Lab is enabled
	 * @return   boolean
	 * @since    1.3.0
	 * @version  1.3.0
	 */
	public function is_enabled() {
		return ( parent::is_enabled() && class_exists( 'FLBuilder' ) );
	}

	/**
	 * Init
	 * @return   void
	 * @since    1.3.0
	 * @version  1.3.0
	 */
	protected function init() {

		add_action( 'init', array( $this, 'load_modules' ) );
		add_action( 'init', array( $this, 'load_templates' ) );

		add_filter( 'llms_page_restricted', array( $this, 'mod_page_restrictions' ), 999, 2 );

		add_filter( 'fl_builder_settings_form_config', array( $this, 'add_node_settings' ) );

		add_filter( 'fl_builder_is_node_visible', array( $this, 'is_node_visible' ), 10, 2 );

		// hide editors when builder is enabled for a post
		add_filter( 'llms_metabox_fields_lifterlms_course_options', array( $this, 'mod_metabox_fields' ) );
		add_filter( 'llms_metabox_fields_lifterlms_membership', array( $this, 'mod_metabox_fields' ) );

		// dolla dolla billz
		add_filter( 'fl_builder_upgrade_url', array( $this, 'upgrade_url' ) );

		// LifterLMS Private Areas
		add_action( 'llms_pa_before_do_area_content', array( $this, 'llms_pa_before_content' ) );
		add_action( 'llms_pa_after_do_area_content', array( $this, 'llms_pa_after_content' ) );

	}

	/**
	 * This function should return array of settings fields
	 * @return   array
	 * @since    1.3.0
	 * @version  1.3.0
	 */
	protected function settings() {
		return array();
	}

	/**
	 * Add settings to BB nodes
	 * @param    array     $form  settings
	 * @return   array
	 * @since    1.3.0
	 * @version  1.3.0
	 */
	public function add_node_settings( $form ) {

		if ( ! isset( $form['class'] ) ) {
			return $form;
		}

		llms_log( $form['class'] );

		// add settings to rows & modules only
		if ( false === strpos( $form['class'], 'fl-builder-row-settings' ) && false === strpos( $form['class'], 'fl-builder-module-settings' ) ) {
			return $form;
		}

		$form['tabs']['lifterlms'] = array(
			'title' => __( 'LifterLMS', 'lifterlms-labs' ),
			'sections' => array(
				'general' => array(
					'title' => '',
					'fields' => array(
						'llms_enrollment_status' => array(
							'type' => 'select',
							'label' => __( 'Display to', 'lifterlms-labs' ),
							'options' => array(
								'' => __( 'Everyone', 'lifterlms-labs' ),
								'enrolled' => __( 'Enrolled Students', 'lifterlms-labs' ),
								'not-enrolled' => __( 'Non-Enrolled Students and Visitors', 'lifterlms-labs' ),
							),
							'toggle' => array(
								'enrolled'  => array(
									'fields' => array( 'llms_enrollment_type' ),
								),
								'not-enrolled'  => array(
									'fields' =>  array( 'llms_enrollment_type' ),
								),
							),
							'help' => __( 'Choose whether to show or hide this element based on LifterLMS enrollment status of the current student.', 'lifterlms'  ),
							'preview' => array(
								'type' => 'none'
							)
						),
						'llms_enrollment_type' => array(
							'type' => 'select',
							'label' => __( 'In', 'lifterlms-labs' ),
							'options' => array(
								'' => __( 'Current Course', 'lifterlms-labs' ),
								'any' => __( 'Any Course(s) or Membership(s)', 'lifterlms-labs' ),
								'specific' => __( 'Specific Course(s) and/or Membership(s)', 'lifterlms-labs' ),
							),
							'toggle' => array(
								'specific'  => array(
									'fields' => array( 'llms_enrollment_match', 'llms_course_ids', 'llms_membership_ids' ),
								),
							),
							'help' => __( 'Select how to check the enrollment status of the current student.', 'lifterlms'  ),
							'preview' => array(
								'type' => 'none'
							)
						),
						'llms_enrollment_match' => array(
							'type' => 'select',
							'label' => __( 'Match', 'lifterlms-labs' ),
							'options' => array(
								'' => __( 'Any of the following', 'lifterlms-labs' ),
								'all' => __( 'All of the following', 'lifterlms-labs' ),
							),
							'help' => __( 'Select how to check the enrollment status of the current student.', 'lifterlms'  ),
							'preview' => array(
								'type' => 'none'
							)
						),
						'llms_course_ids' => array(
							'type' => 'suggest',
							'action' => 'fl_as_posts',
							'data' => 'course',
							// 'matching' => true,
							'label' => __( 'Courses', 'lifterlms-labs' ),
							'help' => __( 'Choose which course(s) the student must be enrolled (or not enrolled) in to view this element.', 'lifterlms'  ),
							'preview' => array(
								'type' => 'none'
							)
						),
						'llms_membership_ids' => array(
							'type' => 'suggest',
							'action' => 'fl_as_posts',
							'data' => 'llms_membership',
							// 'matching' => true,
							'label' => __( 'Memberships', 'lifterlms-labs' ),
							'help' => __( 'Choose which membership(s) the student must be enrolled (or not enrolled) in to view this element.', 'lifterlms'  ),
							'preview' => array(
								'type' => 'none'
							)
						),
					),
				)
			),
		);

		return $form;

	}

	/**
	 * Create a single array of course & membership IDs from a BB node settings object
	 * @param    obj     $settings  BB Node Settings
	 * @return   array
	 * @since    1.3.0
	 * @version  1.3.0
	 */
	private function get_related_posts_from_settings( $settings ) {

		$post_ids = array();

		foreach( array( 'llms_course_ids', 'llms_membership_ids' ) as $key ) {

			if ( ! empty( $settings->$key ) ) {

				$ids = explode( ',', $settings->$key );
				$post_ids = array_merge( $post_ids, $ids );

			}

		}

		return $post_ids;

	}

	/**
	 * Detemine if a student is enrolled in at least one course or membership
	 * @param    int     $uid  WP_User ID
	 * @return   boolean
	 * @since    1.3.0
	 * @version  1.3.0
	 */
	private function is_student_enrollend_in_one_thing( $uid ) {

		if ( ! $uid ) {
			return false;
		}

		$student = llms_get_student( $uid );
		if ( ! $student->exists() ) {
			return false;
		}

		// do we have one course
		$courses = $student->get_courses( array(
			'limit' => 1,
			'status' => 'enrolled',
		) );
		if ( $courses['results'] ) {
			return true;
		}

		// do we have a membership?
		$memberships = $student->get_membership_levels();
		if ( $memberships ) {
			return true;
		}

		// nope
		return false;

	}

	/**
	 * Determine if a node is visible based on llms enrollments status visibility settings
	 * @param    bool     $visible  default visibility
	 * @param    obj      $node     BB node object
	 * @return   boolean
	 * @since    1.3.0
	 * @version  1.3.0
	 */
	public function is_node_visible( $visible, $node ) {

		if ( isset( $node->settings ) && isset( $node->settings->llms_enrollment_status ) ) {

			$status = $node->settings->llms_enrollment_status;

			// no status means it's visible to everyone, we're finished
			if ( ! $status ) {
				return $visible;
			}

			$uid = get_current_user_id();
			$type = ! empty( $node->settings->llms_enrollment_type ) ? $node->settings->llms_enrollment_type : null;

			if ( ! $type || 'any' === $type ) {

				// no type means current course/membership
				if ( ! $type ) {

					$current_id = get_the_ID();
					// cascade up for lessons & quizzes
					if ( in_array( get_post_type( $current_id ), array( 'lesson', 'llms_quiz' ) ) ) {
						$course = llms_get_post_parent_course( $current_id );
						$current_id = $course->get( 'id' );
					}

					// if the current id isn't a course or membership don't proceed
					if ( ! in_array( get_post_type( $current_id ), array( 'course', 'llms_membership' ) ) ) {
						return $visibility;
					}

					// get the eonrllment status
					$enrollment_status = llms_is_user_enrolled( $uid, $current_id );

				}
				// check if they're enrolled/not enrolled in anything
				elseif ( 'any' === $type ) {

					$enrollment_status = $this->is_student_enrollend_in_one_thing( $uid );

				}

				if ( 'enrolled' === $status ) {
					return $enrollment_status;
				} elseif ( 'not-enrolled' === $status ) {
					return ( ! $enrollment_status );
				}

			}

			// check if they're enrolled / not enrolled in the specific courses/memberships
			elseif ( 'specific' === $type ) {

				$match = $node->settings->llms_enrollment_match ? $node->settings->llms_enrollment_match : 'any';
				$ids = $this->get_related_posts_from_settings( $node->settings );

				if ( 'enrolled' === $status ) {

					if ( ! $uid ) {
						return false;
					}

					foreach ( $ids as $id ) {
						if ( llms_is_user_enrolled( $uid, $id ) ) {
							if ( 'any' === $match ) {
								return true;
							}
						} else {
							if ( 'all' === $match ) {
								return false;
							}
						}
					}

					return true;

				} elseif ( 'not-enrolled' === $status ) {

					if ( ! $uid ) {
						return true;
					}

					foreach ( $ids as $id ) {

						// var_dump( sprintf( '%s: %s', $id, llms_is_user_enrolled( $uid, $id ) ) );

						if ( ! llms_is_user_enrolled( $uid, $id ) ) {
							if ( 'any' === $match ) {
								return true;
							}
						} else {
							if ( 'all' === $match ) {
								return false;
							}
						}
					}

					return true;

				}

			}

		}

		return $visible;
	}


	/**
	 * Loads LifterLMS modules
	 * @return   void
	 * @since    1.3.0
	 * @version  1.3.0
	 */
	public function load_modules() {

		if ( file_exists( LLMS_LABS_BB_MODULES_DIR ) ) {
			foreach ( glob( LLMS_LABS_BB_MODULES_DIR . '**/*.php', GLOB_NOSORT ) as $file ) {
				require_once $file;
			}
		}

	}

	/**
	 * Load LifterLMS layout templates
	 * @return   void
	 * @since    1.3.0
	 * @version  1.3.0
	 */
	public function load_templates() {

		FLBuilderModel::register_templates( LLMS_LABS_PLUGIN_DIR . 'inc/labs/inc/' . $this->get_id() . '/templates/course-template.dat' );

	}

	/**
	 * Replace the BB filter after we've rendered our content
	 * @return   void
	 * @since    1.3.1
	 * @version  1.3.1
	 */
	public function llms_pa_after_content() {
		add_filter( 'the_content', 'FLBuilder::render_content' );
	}

	/**
	 * BB will replace PA Post content with course/membership pagebuilder content
	 * so remove the filter and replace when we're done with our output
	 * @return   void
	 * @since    1.3.1
	 * @version  1.3.1
	 */
	public function llms_pa_before_content() {
		remove_filter( 'the_content', 'FLBuilder::render_content' );
	}

	/**
	 * Modify LifterLMS metabox Fields to show the page builder is active
	 * @param    array     $fields  metabox fields
	 * @return   array
	 * @since    1.3.0
	 * @version  1.3.0
	 */
	public function mod_metabox_fields( $fields ) {

		global $post;

		$post_types = array( 'course', 'lesson', 'llms_membership' );

		if ( in_array( $post->post_type, $post_types ) && FLBuilderModel::is_builder_enabled() ) {

			$post_type_obj 	= get_post_type_object( $post->post_type );
			$post_type_name = strtolower( $post_type_obj->labels->singular_name );
			$bb_branding = FLBuilderModel::get_branding();

			$lang = '<p>' . sprintf( _x( '%1$s is currently active for this %2$s.', 'The first %s stands for custom branded "Page Builder" name. The second %s stands for the post type name.', 'lifterlms-labs' ), $bb_branding, $post_type_name ) . '</p>';
			$lang .= '<p><a href="' . FLBuilderModel::get_edit_url() . '" class="fl-launch-builder button button-primary button-large">' . sprintf( _x( 'Launch %s', '%s stands for custom branded "Page Builder" name.', 'lifterlms-labs' ), $bb_branding ). '</a></p>';

			$fields[0]['fields'][0]['type'] = 'custom-html';
			$fields[0]['fields'][0]['value'] = $lang;
			$fields[0]['fields'][1]['type'] = 'custom-html';
			$fields[0]['fields'][1]['value'] = $lang;

		}

		return $fields;

	}

	/**
	 * Bypass restriction checks for courses and memberships when the builder is active
	 * Allows the builder to use custom LifterLMS visibility settings when a student is not enrolled
	 * @param    array     $results  restrcition results data
	 * @param    int     $post_id  current post id
	 * @return   array
	 * @since    1.3.0
	 * @version  1.3.0
	 */
	public function mod_page_restrictions( $results, $post_id ) {

		if ( FLBuilderModel::is_builder_enabled() &&
			 $results['is_restricted'] &&
			 in_array( get_post_type( $post_id ), array( 'course', 'llms_membership' ) ) )
		{

			$results['is_restricted'] = false;
			$results['reason'] = 'bb-lab';

		}

		return $results;

	}

	/**
	 * Registers the LifterLMS Module Category
	 * @param    array     $cats  existing categories
	 * @return   array
	 * @since    1.3.0
	 * @version  1.3.0
	 */
	public function register_category( $cats ) {
		$cats[] = __( 'LifterLMS Modules', 'lifterlms-labs' );
		return $cats;
	}

	/**
	 * #wewantallyourmoniesbecausewerejerks
	 * @param    string     $url  default upgrade url
	 * @return   string
	 * @since    1.3.0
	 * @version  1.3.0
	 */
	public function upgrade_url( $url ) {
		return 'https://www.wpbeaverbuilder.com/?fla=968';
	}

}

return new LLMS_Lab_Beaver_Builder;
