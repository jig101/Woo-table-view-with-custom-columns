<?php
if ( ! defined( 'ABSPATH' ) ) {
		die( );
}
if ( ! class_exists( 'Jplt_Frontend_Helper' ) ) {
/**
 * JpltAdminMeta Class
 *
 * @category Class
 * @package  JpltFrontEndHelper
 * @author    Jiger Patel
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.jigerpatel.co.uk/
 * @since 0.0.1
 */


	class Jplt_Frontend_Helper
	{ 
		
		public $pid;
 
    public function __construct() {
				$this->j_print('Jplt_Frontend_Helper  cons');

    }

    static public function add_scripts( $tblid ) {

        $plugin_url = plugins_url();

        wp_enqueue_style( 'jpltcss', $plugin_url . '/jplt/assets/css/jpltcss.css' );

        wp_enqueue_script( 'jpltjs',  $plugin_url . '/jplt/assets/js/jpltjs.js',array( 'jquery' ), '', true );

        wp_localize_script( 'jpltjs', 'jplt_ajax_params', array( 

            'ajax_url' => admin_url( 'admin-ajax.php' ) ,


            'security' => wp_create_nonce( 'sfdsdf54sd5f4sdf89egtn' ),

            'id' => $tblid

            ) );

        //wp_enqueue_script( 'jpltjs' );

    }
    static public function j_print( $param ) {
      if ( WP_DEBUG === true ) {
        if ( is_array( $param ) || is_object( $param ) ) {
            error_log( print_r( $param, true ) );
        } else {
            error_log( $param );
        }
      }
    }

    static public function the_excerpt_max_charlength($charlength, $excerpt ) {
      $charlength++;
      $r ='';
      if ( mb_strlen( $excerpt ) > $charlength ) {
        $subex = mb_substr( $excerpt, 0, $charlength - 5 );
        $exwords = explode( ' ', $subex );
        $excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
        if ( $excut < 0 ) {
          $r .= mb_substr( $subex, 0, $excut );
        } else {
          $r .=  $subex;
        }
        $r .=  '[...]';
      } else {
        $r .=  $excerpt;
      }
      return $r;
    }    

	}
}
new Jplt_Frontend_Helper();
