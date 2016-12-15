<?php
/**
 * Utility functions
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class LLMS_Labs_LabTech {

	/**
	 * Retrieve an instance of a specific lab by id
	 * @param    string     $id  lab id
	 * @return   false|obj
	 * @since    1.0.0
	 * @version  1.0.0
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
	 * Return an array of all the labs registered with the technician
	 * @return   array
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public static function get_labs() {
		return apply_filters( 'llms_labs_registered_labs', array() );
	}

}