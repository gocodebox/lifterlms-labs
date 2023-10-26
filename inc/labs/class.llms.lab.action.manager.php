<?php
/**
 * Lab: Action Manager
 *
 * @package LifterLMS_Labs/Labs/Classes
 *
 * @since 1.2.0
 * @version [version]
 */

defined( 'ABSPATH' ) || exit;

/**
 * LLMS_Lab_Action Manager Class.
 *
 * Remove LifterLMS Action Hooks with Checkboxes.
 *
 * @since 1.2.0
 * @since 1.5.3 Remove non-functioning course featured video hook.
 */
class LLMS_Lab_Action_Manager extends LLMS_Lab {

	private $hooks = array();

	/**
	 * Configure the Lab.
	 *
	 * @since 1.2.0
	 * @since [version] Escaped strings.
	 *
	 * @return void
	 */
	protected function configure() {

		$this->id = 'action-manager'; // Leave this so we don't have to rewrite db options.
		$this->title = esc_html__( 'Action Manager', 'lifterlms-labs' );
		$this->description = sprintf(
			esc_html__( 'Quickly remove specific elements like course author, syllabus, and more without having to write any code. Click %1$shere%2$s for more information.', 'lifterlms-labs' ),
			'<a href="https://lifterlms.com/docs/lab-action-manager/?utm_source=settings&utm_medium=product&utm_campaign=lifterlmslabsplugin&utm_content=actionmanager">',
			'</a>'
		);

	}

	/**
	 * Initialize the Lab.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	protected function init() {

		$this->setup_hooks();

		add_action( 'plugins_loaded', array( $this, 'remove_actions' ), 777 );

	}

	/**
	 * Remove user-selected actions.
	 *
	 * @since 1.5.3
	 *
	 * @return void
	 */
	public function remove_actions() {

		if ( is_admin() ) {
			return;
		}

		foreach ( $this->hooks as $group ) {
			foreach ( $group['actions'] as $func => $data ) {

				if ( 'yes' === $this->get_option( $func ) ) {

					remove_action( $data['action'], $func, $data['priority'] );

				}

			}
		}

	}

	/**
	 * Lab settings.
	 *
	 * @since 1.2.0
	 * @since [version] Escaped strings.
	 *
	 * @return array
	 */
	public function settings() {
		$settings = array();

		$settings[] = array(
			'type' => 'html',
			'value' => '<em>' . esc_html__( 'Check the box next to each action that should be removed.', 'lifterlms-labs' ) . '</em>',
		);

		foreach ( $this->hooks as $group ) {

			$settings[] = array(
				'type' => 'html',
				'value' => '<strong>' . $group['title'] . '</strong>',
			);

			foreach ( $group['actions'] as $func => $data ) {

				$settings[] = array(
					'columns' => 3,
					'default' => 'no',
					'id' => $func,
					'label' => $data['title'],
					'last_column' => false,
					'required' => false,
					'selected' => ( 'yes' === $this->get_option( $func ) ),
					'style' => 'display:inline-block;margin-bottom:0;',
					'type'  => 'checkbox',
					'value' => 'yes',
				);

			}

			$settings[] = array(
				'type' => 'html',
				'value' => '&nbsp;',
			);

		}
		return $settings;
	}

	/**
	 * Setup a list of available hooks.
	 *
	 * @since 1.2.0
	 * @since 1.5.3 Remove single course featured image since it doesn't work.
	 * @since [version] Escaped strings.
	 *
	 * @return void
	 */
	public function setup_hooks() {
		$this->hooks = array(
			array(
				'title' => esc_html__( 'Single Course Actions', 'lifterlms-labs' ),
				'actions' => array(
					'lifterlms_template_single_video' => array(
						'action' => 'lifterlms_single_course_before_summary',
						'priority' => 20,
						'title' => esc_html__( 'Video Embed', 'lifterlms-labs' ),
					),
					'lifterlms_template_single_audio' => array(
						'action' => 'lifterlms_single_course_before_summary',
						'priority' => 30,
						'title' => esc_html__( 'Audio Embed', 'lifterlms-labs' ),
					),

					'lifterlms_template_single_meta_wrapper_start' => array(
						'action' => 'lifterlms_single_course_after_summary',
						'priority' => 5,
						'title' => esc_html__( 'Meta Information Opening Wrapper', 'lifterlms-labs' ),
					),
					'lifterlms_template_single_length' => array(
						'action' => 'lifterlms_single_course_after_summary',
						'priority' => 10,
						'title' => esc_html__( 'Meta Information: Length', 'lifterlms-labs' ),
					),
					'lifterlms_template_single_difficulty' => array(
						'action' => 'lifterlms_single_course_after_summary',
						'priority' => 20,
						'title' => esc_html__( 'Meta Information: Difficulty', 'lifterlms-labs' ),
					),
					'lifterlms_template_single_course_tracks' => array(
						'action' => 'lifterlms_single_course_after_summary',
						'priority' => 25,
						'title' => esc_html__( 'Meta Information: Tracks', 'lifterlms-labs' ),
					),
					'lifterlms_template_single_course_categories' => array(
						'action' => 'lifterlms_single_course_after_summary',
						'priority' => 30,
						'title' => esc_html__( 'Meta Information: Categories', 'lifterlms-labs' ),
					),
					'lifterlms_template_single_course_tags' => array(
						'action' => 'lifterlms_single_course_after_summary',
						'priority' => 35,
						'title' => esc_html__( 'Meta Information: Tags', 'lifterlms-labs' ),
					),
					'lifterlms_template_course_author' => array(
						'action' => 'lifterlms_single_course_after_summary',
						'priority' => 40,
						'title' => esc_html__( 'Meta Information: Author', 'lifterlms-labs' ),
					),
					'lifterlms_template_single_meta_wrapper_end' => array(
						'action' => 'lifterlms_single_course_after_summary',
						'priority' => 50,
						'title' => esc_html__( 'Meta Information Closing Wrapper', 'lifterlms-labs' ),
					),
					'lifterlms_template_single_prerequisites' => array(
						'action' => 'lifterlms_single_course_after_summary',
						'priority' => 55,
						'title' => esc_html__( 'Prerequisite Information', 'lifterlms-labs' ),
					),
					'lifterlms_template_pricing_table' => array(
						'action' => 'lifterlms_single_course_after_summary',
						'priority' => 60,
						'title' => esc_html__( 'Pricing Table', 'lifterlms-labs' ),
					),
					'lifterlms_template_single_course_progress' => array(
						'action' => 'lifterlms_single_course_after_summary',
						'priority' => 60,
						'title' => esc_html__( 'Progress Bar and "Continue" Button', 'lifterlms-labs' ),
					),
					'lifterlms_template_single_syllabus' => array(
						'action' => 'lifterlms_single_course_after_summary',
						'priority' => 90,
						'title' => esc_html__( 'Syllabus', 'lifterlms-labs' ),
					),
					'lifterlms_template_single_reviews' => array(
						'action' => 'lifterlms_single_course_after_summary',
						'priority' => 100,
						'title' => esc_html__( 'Reviews', 'lifterlms-labs' ),
					),
				),
			),
			array(
				'title' => esc_html__( 'Single Lesson Actions', 'lifterlms-labs' ),
				'actions' => array(
					'lifterlms_template_single_parent_course' => array(
						'action' => 'lifterlms_single_lesson_before_summary',
						'priority' => 10,
						'title' => esc_html__( 'Back to Course Link', 'lifterlms-labs' ),
					),
					'lifterlms_template_single_lesson_video' => array(
						'action' => 'lifterlms_single_lesson_before_summary',
						'priority' => 20,
						'title' => esc_html__( 'Video Embed', 'lifterlms-labs' ),
					),
					'lifterlms_template_single_lesson_audio' => array(
						'action' => 'lifterlms_single_lesson_before_summary',
						'priority' => 20,
						'title' => esc_html__( 'Audio Embed', 'lifterlms-labs' ),
					),

					'lifterlms_template_complete_lesson_link' => array(
						'action' => 'lifterlms_single_lesson_after_summary',
						'priority' => 10,
						'title' => esc_html__( 'Mark Complete / Mark Incomplete / Take Quiz Button(s)', 'lifterlms-labs' ),
					),
					'lifterlms_template_lesson_navigation' => array(
						'action' => 'lifterlms_single_lesson_after_summary',
						'priority' => 20,
						'title' => esc_html__( 'Course Navigation Tiles', 'lifterlms-labs' ),
					),
				),
			),
			array(
				'title' => esc_html__( 'Course and Membership Catalogs', 'lifterlms-labs' ),
				'actions' => array(
					'lifterlms_loop_featured_video' => array(
						'action' => 'lifterlms_before_loop_item',
						'priority' => 8,
						'title' => esc_html__( 'Featured Video', 'lifterlms-labs' ),
					),
					'lifterlms_template_loop_thumbnail' => array(
						'action' => 'lifterlms_before_loop_item_title',
						'priority' => 10,
						'title' => esc_html__( 'Featured Image', 'lifterlms-labs' ),
					),
					'lifterlms_template_loop_progress' => array(
						'action' => 'lifterlms_before_loop_item_title',
						'priority' => 15,
						'title' => esc_html__( 'Progress Bar', 'lifterlms-labs' ),
					),
					'lifterlms_template_loop_author' => array(
						'action' => 'lifterlms_after_loop_item_title',
						'priority' => 10,
						'title' => esc_html__( 'Author', 'lifterlms-labs' ),
					),
					'lifterlms_template_loop_length' => array(
						'action' => 'lifterlms_after_loop_item_title',
						'priority' => 15,
						'title' => esc_html__( 'Length', 'lifterlms-labs' ),
					),
					'lifterlms_template_loop_difficulty' => array(
						'action' => 'lifterlms_after_loop_item_title',
						'priority' => 20,
						'title' => esc_html__( 'Difficulty', 'lifterlms-labs' ),
					),
				),
			),
		);
	}
}

return new LLMS_Lab_Action_Manager();
