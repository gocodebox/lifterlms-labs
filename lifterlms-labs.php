<?php
/**
 * Plugin Name: LifterLMS Labs
 * Plugin URI: https://lifterlms.com/
 * Description: Experimental, conceptual, and possibly silly new features to improve and enhance the functionality of the LifterLMS core
 * Version: 1.6.1
 * Author: LifterLMS
 * Author URI: https://lifterlms.com
 * Text Domain: lifterlms-labs
 * Domain Path: /i18n
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Requires at least: 5.3
 * Tested up to: 6.1.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * LifterLMS Labs Main Class
 *
 * @since 1.0.0
 */
final class LifterLMS_Labs {

	/**
	 * Plugin version
	 *
	 * @var string
	 */
	public $version = '1.6.1';

	/**
	 * Singleton Instance
	 *
	 * @var null|LifterLMS_Labs
	 */
	protected static $_instance = null;

	/**
	 * Main Instance of LifterLMS_Labs class
	 *
	 * @since 1.0.0
	 *
	 * @see llms_labs()
	 *
	 * @return LifterLMS_Labs
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor
	 * @return void
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	private function __construct() {

		do_action( 'llms_labs_load_before' );

		$this->define_constants();

		$this->includes();

		add_action( 'plugins_loaded', array( $this, 'localize' ) );

		do_action( 'llms_labs_load_after' );

	}

	/**
	 * Define Constants
	 * @return void
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	private function define_constants() {

		if ( ! defined( 'LLMS_LABS_PLUGIN_FILE' ) ) {
			define( 'LLMS_LABS_PLUGIN_FILE', __FILE__ );
		}

		if ( ! defined( 'LLMS_LABS_VERSION' ) ) {
			define( 'LLMS_LABS_VERSION', $this->version );
		}

		if ( ! defined( 'LLMS_LABS_PLUGIN_DIR' ) ) {
			define( 'LLMS_LABS_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . plugin_basename( dirname( __FILE__ ) ) . '/' );
		}

	}

	/**
	 * Include required files
	 * @return void
	 * @since   1.0.0
	 * @version 1.1.0
	 */
	private function includes() {

		require_once 'inc/class.llms.labs.labtech.php';
		require_once 'inc/class.llms.labs.settings.page.php';

		require_once 'inc/labs/abstract.llms.lab.php';

		foreach ( glob( LLMS_LABS_PLUGIN_DIR . 'inc/labs/class.llms.lab.*.php', GLOB_NOSORT ) as $lab ) {
			require_once $lab;
		}

	}

	/**
	 * Load Localization files
	 * @return void
	 * @since   1.0.0
	 * @version 1.2.0
	 */
	public function localize() {

		// load locale
		$locale = apply_filters( 'plugin_locale', get_locale(), 'lifterlms-labs' );

		// load a lifterlms specific locale file if one exists
		load_textdomain( 'lifterlms', WP_LANG_DIR . '/lifterlms/lifterlms-labs-' . $locale . '.mo' );

		// load localization files
		load_plugin_textdomain( 'lifterlms', false, dirname( plugin_basename( __FILE__ ) ) . '/i18n' );

	}

}

/**
 * Returns the main instance of LifterLMS Labs
 * @return LifterLMS_Labs
 * @since   1.0.0
 * @version 1.0.0
 */
function llms_labs() {
	return LifterLMS_Labs::instance();
}
return llms_labs();
