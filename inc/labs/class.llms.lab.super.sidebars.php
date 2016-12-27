<?php
/**
 * Super Sidebars
 * Make LifterLMS Sidebar Theme Compatibility Easy
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class LLMS_Lab_Super_Sidebars extends LLMS_Lab {

	/**
	 * Configure the Lab
	 * @return   void
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	protected function configure() {
		$this->id = 'super-sidebars';
		$this->title = __( 'Super Sidebars', 'lifterlms-labs' );
		$this->description = sprintf( __( 'Very quickly configure LifterLMS sidebars to work with your theme. For help and more information click %1$shere%2$s.', 'lifterlms-labs' ), '<a href="https://lifterlms.com/docs/super-sidebars-lab?utm_source=settings&utm_campaign=lifterlmslabsplugin&utm_medium=product&utm_content=supersidebars" target="blank">', '</a>' );
	}

	/**
	 * Initialize the Lab
	 * @return   void
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	protected function init() {

		// replace the default sidebar with user selected sidebar
		add_filter( 'llms_get_theme_default_sidebar', array( $this, 'replace_sidebar' ) );

	}

	/**
	 * Replace the default sidebar with user selected sidebar
	 * @param    string     $id  default sidebar
	 * @return   string
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function replace_sidebar( $id ) {
		$sidebar = $this->get_option( 'main_sidebar' );
		if ( $sidebar ) {
			$id = $sidebar;
		}
		return $id;
	}

	/**
	 * Define the lab's settings
	 * @return   array
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	protected function settings() {

		global $wp_registered_sidebars;

		$sidebars = wp_list_pluck( $wp_registered_sidebars, 'name', 'id' );

		unset( $sidebars['llms_course_widgets_side'] );
		unset( $sidebars['llms_lesson_widgets_side'] );

		return array(
			array(
				'columns' => 6,
				'classes' => 'llms-select2',
				'description' => '<br>' . __( 'Select your theme\'s main sidebar, this is usually the sidebar that displays when viewing a blog post.', 'lifterlms' ),
				'id' => 'llms-lab-main-sidebar',
				'label' => __( 'Main Sidebar', 'lifterlms-labs' ) . '<br>',
				'last_column' => true,
				'name' => 'main_sidebar',
				'options' => $sidebars,
				'required' => false,
				'selected' => '',
				'type'  => 'select',
				'value' => $this->get_option( 'main_sidebar' ),
			),
		);
	}

}

return new LLMS_Lab_Super_Sidebars;