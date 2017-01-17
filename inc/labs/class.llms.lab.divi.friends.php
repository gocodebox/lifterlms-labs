<?php
/**
 * Divi Friends
 * Divi theme compatibility
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class LLMS_Lab_Divi_Friends extends LLMS_Lab {

	/**
	 * Configure the Lab
	 * @return   void
	 * @since    1.1.0
	 * @version  1.1.0
	 */
	protected function configure() {
		$this->id = 'divi-friends';
		$this->title = __( 'Divi Friends', 'lifterlms-labs' );
		$this->description = __( 'Make LifterLMS and Divi be the bestest of friends.', 'lifterlms-labs' );
	}

	/**
	 * Initialize the Lab
	 * @return   void
	 * @since    1.1.0
	 * @version  1.1.1
	 */
	protected function init() {

		add_action( 'init', array( $this, 'remove_llms_sidebars' ), 15 );
		add_action( 'lifterlms_before_main_content', array( $this, 'output_content_wrapper_start' ), 10 );
		add_action( 'lifterlms_after_main_content', array( $this, 'output_content_wrapper_end' ), 10 );

		add_filter( 'body_class', array( $this, 'body_class' ), 777 );

	}

	/**
	 * Late initialization for removal of lifterlms sidebars
	 * @return    void
	 * @since     1.1.0
	 * @version   1.1.1
	 */
	public function remove_llms_sidebars() {
		remove_action( 'lifterlms_sidebar', 'lifterlms_get_sidebar', 10 );
	}

	/**
	 * Output the opening Divi content wrapper tags
	 * @return   void
	 * @since    1.1.0
	 * @version  1.1.0
	 */
	public function output_content_wrapper_start() {
		echo '
			<div id="main-content">
				<div class="container">
					<div id="content-area" class="clearfix">';
		echo '<div id="left-area">';
	}

	/**
	 * Output the closing Divi content wrapper tags
	 * @return   void
	 * @since    1.1.0
	 * @version  1.1.0
	 */
	public function output_content_wrapper_end() {
		echo '</div><!-- #left-area -->';
		echo '
					</div> <!-- #content-area -->
				</div> <!-- .container -->
			</div> <!-- #main-content -->';
	}

	/**
	 * Remove sidebar classes from the body and add the full-width class
	 * @param    array     $classes  array of body css classes
	 * @return   array
	 * @since    1.1.0
	 * @version  1.1.0
	 */
	public function body_class( $classes ) {

		if ( is_courses() || is_memberships() ) {

			// remove all layouts
			foreach ( array( 'et_right_sidebar', 'et_left_sidebar', 'et_full_width_page' ) as $class ) {
				$key = array_search( $class, $classes );
				if ( false !== $key ) {
					unset( $key );
				}
			}

			// add the layout we want / settings with default to full width
			$classes[] = 'et_full_width_page';

		}

		return $classes;

	}

	/**
	 * This Lab doesn't have any settings
	 * @return   array
	 * @since    1.1.0
	 * @version  1.1.0
	 */
	public function settings() {
		return array();
	}

}

return new LLMS_Lab_Divi_Friends();