<?php
/**
 * LifterLMS Course/Membership Pricing Table Module HTML
 * @since    1.3.0
 * @version  1.3.0
 */

// Restrict direct access
if ( ! defined( 'ABSPATH' ) ) { exit; }

class LLMS_Lab_Pricing_Table_Module extends FLBUilderModule {

	/**
	 * Constructor
	 * @since    1.3.0
	 * @version  1.3.0
	 */
	public function __construct() {
		parent::__construct( array(
			'name' => __( 'Pricing Table', 'lifterlms-labs' ),
			'description' => __( 'LifterLMS Course / Membership Pricing Table', 'lifterlms-labs' ),
			'category' => __( 'LifterLMS Modules', 'lifterlms-labs' ),
			'dir' => LLMS_LABS_BB_MODULES_DIR . 'pricing-table/',
			'url' => LLMS_LABS_BB_MODULES_URL . 'pricing-table/',
			'editor_export' => false,
			'enabled' => true,
		) );

		// ensure pricing tables always display when used within a BB module
		add_action( 'llms_lab_bb_before_pricing_table', array( $this, 'add_force_show_table_filter' ) );
		add_action( 'lifterlms_after_access_plans', array( $this, 'remove_force_show_table_filter' ) );

		// ensure pricing tables always display when the frontend builder is active
		add_filter( 'llms_product_pricing_table_enrollment_status', array( $this, 'show_table' ) );

	}

	/**
	 * Force display of pricing tables within BB modules
	 * @return   void
	 * @since    1.3.0
	 * @version  1.3.0
	 */
	public function add_force_show_table_filter() {
		add_filter( 'llms_product_pricing_table_enrollment_status', '__return_false' );
	}

	/**
	 * Remove force display after pricing tables within BB modules
	 * @return   void
	 * @since    1.3.0
	 * @version  1.3.0
	 */
	public function remove_force_show_table_filter() {
		remove_filter( 'llms_product_pricing_table_enrollment_status', '__return_false' );
	}

	/**
	 * Get the product ID to be used based of BB module settings
	 * @param    obj     $settings  BB node settings object
	 * @return   int|false
	 * @since    1.3.0
	 * @version  1.3.0
	 */
	public function get_product_id( $settings ) {

		$type = $settings->llms_product_type;
		if ( ! $type ) {
			$id = get_the_ID();
		} elseif ( 'course' === $type || 'membership' === $type ) {
			$key = sprintf( 'llms_%s_id', $type );
			$id = $settings->$key;
		}

		if ( in_array( get_post_type( $id ), array( 'lesson', 'llms_quiz' ) ) ) {
			$course = llms_get_post_parent_course( $id );
			$id = $course->get( 'id' );
		}

		// if the current id isn't a course or membership don't proceed
		if ( ! in_array( get_post_type( $id ), array( 'course', 'llms_membership' ) ) ) {
			return false;
		}

		return $id;

	}

	/**
	 * Always show the pricing table when the builder is active
	 * @param    bool     $enrollment  enrollment status of the current user
	 * @return   bool
	 * @since    1.3.0
	 * @version  1.3.0
	 */
	public function show_table( $enrollment ) {

		if ( FLBuilderModel::is_builder_active() ) {
			return false;
		}

		return $enrollment;

	}

}

FLBuilder::register_module( 'LLMS_Lab_Pricing_Table_Module', array(
	'general' => array(
		'title' => __( 'General', 'lifterlms-labs' ),
		'sections' => array(
			'general' => array(
				'title' => __( 'General', 'lifterlms-labs' ),
				'fields' => array(
					'llms_product_type' => array(
						'type' => 'select',
						'label' => __( 'Product Type', 'lifterlms-labs' ),
						'options' => array(
							'' => __( 'Current Course or Membership', 'lifterlms-labs' ),
							'course' => __( 'Course', 'lifterlms-labs' ),
							'membership' => __( 'Memebership', 'lifterlms-labs' ),
						),
						'toggle' => array(
							'course'  => array(
								'fields' => array( 'llms_course_id' ),
							),
							'membership'  => array(
								'fields' => array( 'llms_membership_id' ),
							),
						),
						'preview' => array(
							'type' => 'none'
						)
					),
					'llms_course_id' => array(
						'type' => 'suggest',
						'action' => 'fl_as_posts',
						'data' => 'course',
						'limit' => 1,
						'label' => __( 'Course', 'lifterlms-labs' ),
						'help' => __( 'Choose which course to display a pricing table for.', 'lifterlms'  ),
						'preview' => array(
							'type' => 'none'
						)
					),
					'llms_membership_id' => array(
						'type' => 'suggest',
						'action' => 'fl_as_posts',
						'data' => 'llms_membership',
						'limit' => 1,
						'label' => __( 'Membership', 'lifterlms-labs' ),
						'help' => __( 'Choose which membership to display a pricing table for.', 'lifterlms'  ),
						'preview' => array(
							'type' => 'none'
						)
					),
				),
			),
		),
	)
) );
