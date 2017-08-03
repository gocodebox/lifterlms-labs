<?php
/**
 * Lab Abstract
 *
 * Labs should extend this class
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

abstract class LLMS_Lab {

	protected $id = '';
	private $enabled = 'no';
	protected $title = '';
	protected $description = '';

	public function __construct() {

		// run configuration
		$this->configure();

		// register the lab with the lab technician
		add_filter( 'llms_labs_registered_labs', array( $this, 'register' ) );

		// call a function on lab activation
		add_action( 'llms_lab_' . $this->get_id() . '_enabled', array( $this, 'on_enable' ) );

		// call a function on lab deactivation
		add_action( 'llms_lab_' . $this->get_id() . '_disabled', array( $this, 'on_disable' ) );

		if ( $this->is_enabled() ) {

			$this->init();

		}

	}

	/**
	 * This function should define lab vars
	 * It's run during contruction before anything else happens
	 * @return   void
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	abstract protected function configure();

	/**
	 * This function should do whatever the lab does
	 * It's run after during construction after configuration and default functions are run
	 * @return   void
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	abstract protected function init();

	/**
	 * This function should return array of settings fields
	 * @return   array
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	abstract protected function settings();

	/**
	 * Get the description for a lab
	 * @return   string
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function get_description() {
		return apply_filters( 'llms_lab_' . $this->get_id() . '_get_description', $this->description, $this );
	}

	/**
	 * Get the ID of the Lab
	 * @return   string
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Retrieve an option from the wp_options table
	 * Automatically prepends the labs id as a prefix
	 * @param    string     $key      unprefixed option key
	 * @param    mixed      $default  default value for the option
	 * @return   mixed
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function get_option( $key, $default = null ) {
		return apply_filters( 'llms_lab_' . $this->get_id() . '_get_option', get_option( $this->get_option_name( $key ), $default ), $key, $default, $this );
	}

	/**
	 * Get the auto-prefixed name for an option
     * @param    string     $key      unprefixed option key
	 * @return   string
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	protected function get_option_name( $key ) {
		return apply_filters( 'llms_lab_' . $this->get_id() . '_get_option_prefix', 'llms_lab_' . $this->get_id() . '_' . $key, $this );
	}

	/**
	 * Retrieve a filterable array of settings for the lab
	 * @return   array
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function get_settings() {
		return apply_filters( 'llms_lab_' . $this->get_id() . '_get_settings', $this->settings(), $this );
	}

	/**
	 * Get the tilte for a lab
	 * @return   string
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function get_title() {
		return apply_filters( 'llms_lab_' . $this->get_id() . '_get_title', $this->title, $this );
	}

	/**
	 * Determine if the Lab is enabled
	 * @return   boolean
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function is_enabled() {
		return ( 'yes' === $this->get_option( 'enabled', 'no' ) );
	}

	/**
	 * Stub function called when lab is disabled
	 * @return   void
	 * @since    1.2.0
	 * @version  1.2.0
	 */
	public function on_disable() {}

	/**
	 * Stub function called when lab is enabled
	 * @return   void
	 * @since    1.2.0
	 * @version  1.2.0
	 */
	public function on_enable() {}

	/**
	 * Register the lab with the lab technician
	 * @param    array     $labs  array of registered labs
	 * @return   array
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function register( $labs ) {
		$labs[ $this->get_id() ] = $this;
		return $labs;
	}

	/**
	 * Set an option from the wp_options table
	 * Automatically prepends the labs id as a prefix
	 * @param    string     $key      unprefixed option key
	 * @param    mixed      $default  default value for the option
	 * @return   mixed
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function set_option( $key, $val, $autoload = false ) {
		return apply_filters( 'llms_lab_' . $this->get_id() . '_set_option', update_option( $this->get_option_name( $key ), $val, $autoload ), $key, $val, $autoload, $this );
	}

}
