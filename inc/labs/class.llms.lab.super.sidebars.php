<?php
/**
 * Super Sidebars
 *
 * @package LifterLMS_Labs/Labs/Classes
 *
 * @since 1.0.0
 * @version 1.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Super Sidebars class.
 *
 * Make LifterLMS Sidebar Theme Compatibility Easy
 *
 * @since 1.0.0
 */
class LLMS_Lab_Super_Sidebars extends LLMS_Lab {

	/**
	 * Configure the Lab.
	 *
	 * @since 1.0.0
	 * @since 1.7.0 Escaped strings.
	 *
	 * @return void
	 */
	protected function configure() {
		$this->id          = 'super-sidebars';
		$this->title       = esc_html__( 'Super Sidebars', 'lifterlms-labs' );
		$this->description = sprintf(
			// Translators: %1$s = Opening anchor tag; %2$s = Closing anchor tag.
			esc_html__( 'Very quickly configure LifterLMS sidebars to work with your theme. For help and more information click %1$shere%2$s.', 'lifterlms-labs' ),
			'<a href="https://lifterlms.com/docs/super-sidebars-lab?utm_source=settings&utm_campaign=lifterlmslabsplugin&utm_medium=product&utm_content=supersidebars" target="blank">',
			'</a>'
		);
	}

	/**
	 * Initialize the Lab.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function init() {
		// Replace the default sidebar with user selected sidebar.
		add_filter( 'llms_get_theme_default_sidebar', array( $this, 'replace_sidebar' ) );
	}

	/**
	 * Replace the default sidebar with user selected sidebar.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Default sidebar ID.
	 * @return string
	 */
	public function replace_sidebar( $id ) {
		$sidebar = $this->get_option( 'main_sidebar' );
		if ( $sidebar ) {
			$id = $sidebar;
		}
		return $id;
	}

	/**
	 * Define the lab's settings.
	 *
	 * @since 1.0.0
	 * @since 1.7.0 Escaped strings.
	 *
	 * @return array
	 */
	protected function settings() {

		global $wp_registered_sidebars;

		$sidebars = wp_list_pluck( $wp_registered_sidebars, 'name', 'id' );

		unset( $sidebars['llms_course_widgets_side'] );
		unset( $sidebars['llms_lesson_widgets_side'] );

		return array(
			array(
				'columns'     => 6,
				'classes'     => 'llms-select2',
				'description' => '<br>' . esc_html__( 'Select your theme\'s main sidebar, this is usually the sidebar that displays when viewing a blog post.', 'lifterlms-labs' ),
				'id'          => 'llms-lab-main-sidebar',
				'label'       => esc_html__( 'Main Sidebar', 'lifterlms-labs' ) . '<br>',
				'last_column' => true,
				'name'        => 'main_sidebar',
				'options'     => $sidebars,
				'required'    => false,
				'selected'    => '',
				'type'        => 'select',
				'value'       => $this->get_option( 'main_sidebar' ),
			),
		);
	}

}

return new LLMS_Lab_Super_Sidebars();
