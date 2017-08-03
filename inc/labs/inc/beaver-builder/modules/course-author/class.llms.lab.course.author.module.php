<?php
/**
 * LifterLMS Course Author Module
 * @since    1.3.0
 * @version  1.3.0
 */

// Restrict direct access
if ( ! defined( 'ABSPATH' ) ) { exit; }

class LLMS_Lab_Course_Author_Module extends FLBUilderModule {

	/**
	 * Constructor
	 * @since    1.3.0
	 * @version  1.3.0
	 */
	public function __construct() {
		parent::__construct( array(
			'name' => __( 'Course Author', 'lifterlms-labs' ),
			'description' => __( 'Displays the name, author, and bio for the author of a course.', 'lifterlms-labs' ),
			'category' => __( 'LifterLMS Modules', 'lifterlms-labs' ),
			'dir' => LLMS_LABS_BB_MODULES_DIR . 'course-author/',
			'url' => LLMS_LABS_BB_MODULES_URL . 'course-author/',
			'editor_export' => false,
			'enabled' => true,
		) );

	}

}

FLBuilder::register_module( 'LLMS_Lab_Course_Author_Module', array(
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
						'help' => __( 'Select the course to display the author from. Leave blank for the current course.', 'lifterlms'  ),
						'preview' => array(
							'type' => 'none'
						)
					),
					'llms_avatar_size' => array(
						'default' => 48,
						'type' => 'unit',
						'label' => __( 'Avatar Size', 'lifterlms-labs' ),
						'description' => 'px',
						'preview' => array(
							'type' => 'none'
						),
					),
					'llms_show_bio' => array(
						'type' => 'select',
						'label' => __( 'Display Author Bio', 'lifterlms-labs' ),
						'options' => array(
							'no' => __( 'No', 'lifterlms-labs' ),
							'yes' => __( 'Yes', 'lifterlms-labs' ),
						),
						'preview' => array(
							'type' => 'none'
						)
					),
				),
			),
		),
	)
) );
