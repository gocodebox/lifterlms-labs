<?php
/**
 * LifterLMS Course Progress Bar Module
 * @since    1.3.0
 * @version  1.3.0
 */

// Restrict direct access
if ( ! defined( 'ABSPATH' ) ) { exit; }

class LLMS_Lab_Course_Progress_Bar_Module extends FLBUilderModule {

	/**
	 * Constructor
	 * @since    1.3.0
	 * @version  1.3.0
	 */
	public function __construct() {
		parent::__construct( array(
			'name' => __( 'Course Progress Bar', 'lifterlms-labs' ),
			'description' => __( 'Displays a course progress bar for the current course.', 'lifterlms-labs' ),
			'category' => __( 'LifterLMS Modules', 'lifterlms-labs' ),
			'dir' => LLMS_LABS_BB_MODULES_DIR . 'course-progress-bar/',
			'url' => LLMS_LABS_BB_MODULES_URL . 'course-progress-bar/',
			'editor_export' => false,
			'enabled' => true,
		) );

	}

}

FLBuilder::register_module( 'LLMS_Lab_Course_Progress_Bar_Module', array() );
