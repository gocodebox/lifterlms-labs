<?php
/**
 * Utility functions.
 *
 * @package LifterLMS_Labs/Classes
 *
 * @since 1.0.0
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Utility functions class.
 *
 * @since 1.0.0
 */
class LLMS_Labs_LabTech {

	/**
	 * Retrieve an instance of a specific lab by id.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Lab id.
	 * @return false|obj
	 */
	public static function get_lab( $id ) {
		$labs = self::get_labs();
		if ( isset( $labs[ $id ] ) ) {
			return $labs[ $id ];
		} else {
			return false;
		}
	}

	/**
	 * Return an array of all the labs registered with the technician.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_labs() {
		return apply_filters( 'llms_labs_registered_labs', array() );
	}

}
