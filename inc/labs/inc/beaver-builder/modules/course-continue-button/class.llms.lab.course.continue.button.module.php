<?php
/**
 * LifterLMS Course Continue Button Module
 * @since    1.3.0
 * @version  1.3.0
 */

// Restrict direct access
if ( ! defined( 'ABSPATH' ) ) { exit; }

class LLMS_Lab_Course_Continue_Button_Module extends FLBUilderModule {

	/**
	 * Constructor
	 * @since    1.3.0
	 * @version  1.3.0
	 */
	public function __construct() {
		parent::__construct( array(
			'name' => __( 'Course Continue Button', 'lifterlms-labs' ),
			'description' => __( 'Displays a course progress bar for the current course.', 'lifterlms-labs' ),
			'category' => __( 'LifterLMS Modules', 'lifterlms-labs' ),
			'dir' => LLMS_LABS_BB_MODULES_DIR . 'course-continue-button/',
			'url' => LLMS_LABS_BB_MODULES_URL . 'course-continue-button/',
			'editor_export' => false,
			'enabled' => true,
		) );

		add_filter( 'llms_course_continue_button_next_lesson', array( $this, 'force_display' ) );

	}

	public function force_display( $lesson_id ) {

		if ( ! $lesson_id && FLBuilderModel::is_builder_active() ) {
			return get_the_ID();
		}

		return $lesson_id;

	}

}

FLBuilder::register_module( 'LLMS_Lab_Course_Continue_Button_Module', array(
	'general' => array(
		'title' => __( 'General', 'lifterlms-labs' ),
		'sections' => array(
			'general' => array(
				'title' => __( 'General', 'lifterlms-labs' ),
				'fields' => array(
					'llms_course_id' => array(
						'type' => 'suggest',
						'action' => 'fl_as_posts',
						'data' => 'course',
						'limit' => 1,
						'label' => __( 'Course', 'lifterlms-labs' ),
						'help' => __( 'Select the course to display a continue button for. Leave blank for the current course.', 'lifterlms'  ),
						'preview' => array(
							'type' => 'none'
						)
					),
				),
			),
		),
	)
) );
