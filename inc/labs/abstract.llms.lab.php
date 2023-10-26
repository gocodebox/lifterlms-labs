<?php
/**
 * Lab Abstract.
 *
 * Labs should extend this class
 *
 * @package LifterLMS_Labs/Labs/Classes
 *
 * @since 1.0.0
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Lab Abstract class.
 *
 * @since 1.0.0
 */
abstract class LLMS_Lab {

	/**
	 * ID.
	 *
	 * @var string
	 */
	protected $id = '';

	/**
	 * Enabled lab.
	 *
	 * @var string
	 */
	private $enabled = 'no';

	/**
	 * Lab's title.
	 *
	 * @var string
	 */
	protected $title = '';

	/**
	 * Lab's description.
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {

		// Run configuration.
		$this->configure();

		// Register the lab with the lab technician.
		add_filter( 'llms_labs_registered_labs', array( $this, 'register' ) );

		// Call a function on lab activation.
		add_action( 'llms_lab_' . $this->get_id() . '_enabled', array( $this, 'on_enable' ) );

		// Call a function on lab deactivation.
		add_action( 'llms_lab_' . $this->get_id() . '_disabled', array( $this, 'on_disable' ) );

		if ( $this->is_enabled() ) {

			$this->init();

		}

	}

	/**
	 * This function should define lab vars.
	 *
	 * It's run during construction before anything else happens.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	abstract protected function configure();

	/**
	 * This function should do whatever the lab does.
	 *
	 * It's run after during construction after configuration and default functions are run.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	abstract protected function init();

	/**
	 * This function should return array of settings fields.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	abstract protected function settings();

	/**
	 * Get the description for a lab.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_description() {
		return apply_filters( 'llms_lab_' . $this->get_id() . '_get_description', $this->description, $this );
	}

	/**
	 * Get the ID of the Lab.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Retrieve an option from the wp_options table.
	 *
	 * Automatically prepends the labs id as a prefix.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key     Unprefixed option key.
	 * @param mixed  $default Default value for the option.
	 * @return mixed
	 */
	public function get_option( $key, $default = null ) {
		return apply_filters( 'llms_lab_' . $this->get_id() . '_get_option', get_option( $this->get_option_name( $key ), $default ), $key, $default, $this );
	}

	/**
	 * Get the auto-prefixed name for an option.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Unprefixed option key.
	 * @return string
	 */
	protected function get_option_name( $key ) {
		return apply_filters( 'llms_lab_' . $this->get_id() . '_get_option_prefix', 'llms_lab_' . $this->get_id() . '_' . $key, $this );
	}

	/**
	 * Retrieve a filterable array of settings for the lab.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_settings() {
		return apply_filters( 'llms_lab_' . $this->get_id() . '_get_settings', $this->settings(), $this );
	}

	/**
	 * Get the title for a lab.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_title() {
		return apply_filters( 'llms_lab_' . $this->get_id() . '_get_title', $this->title, $this );
	}

	/**
	 * Determine if the Lab is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return ( 'yes' === $this->get_option( 'enabled', 'no' ) );
	}

	/**
	 * Stub function called when lab is disabled.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function on_disable() {}

	/**
	 * Stub function called when lab is enabled.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function on_enable() {}

	/**
	 * Register the lab with the lab technician.
	 *
	 * @since 1.0.0
	 *
	 * @param array $labs Array of registered labs.
	 * @return array
	 */
	public function register( $labs ) {
		$labs[ $this->get_id() ] = $this;
		return $labs;
	}

	/**
	 * Set an option from the wp_options table.
	 *
	 * Automatically prepends the labs id as a prefix.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key      Unprefixed option key.
	 * @param mixed  $val      Value of the option.
	 * @param bool   $autoload Whether or not to autoload the option.
	 * @return mixed
	 */
	public function set_option( $key, $val, $autoload = false ) {
		return apply_filters( 'llms_lab_' . $this->get_id() . '_set_option', update_option( $this->get_option_name( $key ), $val, $autoload ), $key, $val, $autoload, $this );
	}

}
