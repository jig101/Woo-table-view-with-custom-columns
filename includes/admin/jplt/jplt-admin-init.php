<?php
/**
 * JpltAdminInit Class file
 *
 * @category Class
 * @package  Jplt
 * @author    Jiger Patel
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.jigerpatel.co.uk/
 * @since 0.0.1
 */


if ( ! class_exists( 'JpltAdminInit' ) ) {
	/**
	 * JpltAdminInit Class
	 *
	 * @category Class
	 * @package  Jplt
	 * @author    Jiger Patel
	 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
	 * @link     http://www.jigerpatel.co.uk/
	 * @since 0.0.1
	 */
	class JpltAdminInit
	{
		/**
		 * [__construct Load up admin helper functions]
		 */
		public function __construct() {
			//JpltAdminInit::j_print( 'JpltAdminInit Class' );
		}
		/**
		 * [j_print Prints log data to wordpress log file /wp-content/debug]
		 *
		 * @param  [array,string] $param Can take array or string.
		 */
		static public function j_print( $param ) {
			if ( WP_DEBUG === true ) {
				if ( is_array( $param ) || is_object( $param ) ) {
						error_log( print_r( $param, true ) );
				} else {
						error_log( $param );
				}
			}
		}
	}
}
new JpltAdminInit();
