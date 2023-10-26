<?php
/**
 * LifterLMS Course Author Module
 *
 * @package LifterLMS_Labs/Labs/BeaverBuilder/Modules/CourseAuthor/Classes
 *
 * @since 1.3.0
 * @version 1.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * LifterLMS Course Author Module class.
 *
 * @since 1.3.0
 */
class LLMS_Lab_Course_Author_Module extends FLBUilderModule {

	/**
	 * Constructor.
	 *
	 * @since 1.3.0
	 * @since 1.7.0 Escape strings.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name'          => esc_html__( 'Course Author', 'lifterlms-labs' ),
				'description'   => esc_html__( 'Displays the name, author, and bio for the author of a course.', 'lifterlms-labs' ),
				'category'      => esc_html__( 'LifterLMS Modules', 'lifterlms-labs' ),
				'dir'           => LLMS_LABS_BB_MODULES_DIR . 'course-author/',
				'url'           => LLMS_LABS_BB_MODULES_URL . 'course-author/',
				'editor_export' => false,
				'enabled'       => true,
			)
		);

	}

}

FLBuilder::register_module(
	'LLMS_Lab_Course_Author_Module',
	array(
		'general' => array(
			'title'    => esc_html__( 'General', 'lifterlms-labs' ),
			'sections' => array(
				'general' => array(
					'title'  => esc_html__( 'General', 'lifterlms-labs' ),
					'fields' => array(
						'llms_course_id'   => array(
							'type'    => 'suggest',
							'action'  => 'fl_as_posts',
							'data'    => 'course',
							'limit'   => 1,
							'label'   => esc_html__( 'Course', 'lifterlms-labs' ),
							'help'    => esc_html__( 'Select the course to display the author from. Leave blank for the current course.', 'lifterlms-labs' ),
							'preview' => array(
								'type' => 'none',
							),
						),
						'llms_avatar_size' => array(
							'default'     => 48,
							'type'        => 'unit',
							'label'       => esc_html__( 'Avatar Size', 'lifterlms-labs' ),
							'description' => 'px',
							'preview'     => array(
								'type' => 'none',
							),
						),
						'llms_show_bio'    => array(
							'type'    => 'select',
							'label'   => esc_html__( 'Display Author Bio', 'lifterlms-labs' ),
							'options' => array(
								'no'  => esc_html__( 'No', 'lifterlms-labs' ),
								'yes' => esc_html__( 'Yes', 'lifterlms-labs' ),
							),
							'preview' => array(
								'type' => 'none',
							),
						),
					),
				),
			),
		),
	)
);
