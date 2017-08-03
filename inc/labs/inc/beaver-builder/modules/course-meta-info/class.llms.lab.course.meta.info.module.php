<?php
/**
 * LifterLMS Course Meta Info Module
 * @since    1.3.0
 * @version  1.3.0
 */

// Restrict direct access
if ( ! defined( 'ABSPATH' ) ) { exit; }

class LLMS_Lab_Course_Meta_Info_Module extends FLBUilderModule {

	/**
	 * Constructor
	 * @since    1.3.0
	 * @version  1.3.0
	 */
	public function __construct() {
		parent::__construct( array(
			'name' => __( 'Course Information', 'lifterlms-labs' ),
			'description' => __( 'Displays course information: length, difficulty, tracks, categories, and tags.', 'lifterlms-labs' ),
			'category' => __( 'LifterLMS Modules', 'lifterlms-labs' ),
			'dir' => LLMS_LABS_BB_MODULES_DIR . 'course-meta-info/',
			'url' => LLMS_LABS_BB_MODULES_URL . 'course-meta-info/',
			'editor_export' => false,
			'enabled' => true,
		) );

	}

}

FLBuilder::register_module( 'LLMS_Lab_Course_Meta_Info_Module', array(
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
						'help' => __( 'Select the course to display the course information from. Leave blank for the current course.', 'lifterlms'  ),
						'preview' => array(
							'type' => 'none'
						)
					),
				),
			),
		),
	)
) );
