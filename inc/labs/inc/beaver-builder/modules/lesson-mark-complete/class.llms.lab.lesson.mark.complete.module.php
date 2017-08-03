<?php
/**
 * LifterLMS Lesson Mark Complete Module
 * @since    1.3.0
 * @version  1.3.0
 */

// Restrict direct access
if ( ! defined( 'ABSPATH' ) ) { exit; }

class LLMS_Lab_Lesson_Mark_Complete_Module extends FLBUilderModule {

	/**
	 * Constructor
	 * @since    1.3.0
	 * @version  1.3.0
	 */
	public function __construct() {
		parent::__construct( array(
			'name' => __( 'Lesson Mark Complete Button', 'lifterlms-labs' ),
			'description' => __( 'Displays the mark complete / incomplete button(s) for a lesson', 'lifterlms-labs' ),
			'category' => __( 'LifterLMS Modules', 'lifterlms-labs' ),
			'dir' => LLMS_LABS_BB_MODULES_DIR . 'lesson-mark-complete/',
			'url' => LLMS_LABS_BB_MODULES_URL . 'lesson-mark-complete/',
			'editor_export' => false,
			'enabled' => true,
		) );

	}

}

FLBuilder::register_module( 'LLMS_Lab_Lesson_Mark_Complete_Module', array() );
