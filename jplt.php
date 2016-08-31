<?php
/**
	Plugin Name: JP Woocommerce custom product table display plugin
	Description:
	Plugin URI: http://www.jigerpatel.co.uk
	Author: Jiger Patel
	Author URI: http://www.jigerpatel.co.uk
	Version: 1.0
	License: GPL2

	@category
	@package  Jplt
	@author    Jiger Patel
	@license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
	@link     http://www.jigerpatel.co.uk/
	@since 0.0.1
 **/

/*
	Copyright (C) Year  Author  Email

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if ( ! defined( 'ABSPATH' ) ) {
		die( "Can't load this file directly" );
}

if ( ! function_exists( 'jplt_check_wc' ) ) {
	/**
	 * [jplt_check_wc Check if the Woo main plugin is active.
	 *
	 * @return [html] Notify user if woo plugin is not active.
	 */
	function jplt_check_wc() {
		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'jplt_install_woocommerce_admin_notice' );
			
			return;
		}
	}
}
add_action( 'plugins_loaded', 'jplt_check_wc' );

if ( ! function_exists( 'jplt_install_woocommerce_admin_notice' ) ) {
	/**
	 * [jplt_install_woocommerce_admin_notice] Admin notice output.
	 *
	 * [html] Notify user if woo plugin is not active.
	 */
	function jplt_install_woocommerce_admin_notice() {
		wp_kses( __( '<div class="error"><p>Ready to publish your first post? <a href="%1$s">Get started here</a>.</p></div>', 'quark' ),
			array(
				'a' => array(
						'herf' => array(),
					),
				'div' => array(),
		) );
	}
}

require_once( plugin_dir_path( __FILE__ ) .  '/includes/frontend/jplt-front-end-helper.php' );
require_once( plugin_dir_path( __FILE__ ) .  '/includes/frontend/jplt-table.php' );
//require_once( plugin_dir_path( __FILE__ ) .  '/includes/frontend/jplt-query.php' );

if ( ! shortcode_exists( 'JPDisplayTable' ) ) {
		add_shortcode( 'JPDisplayTable', 'jplt_shortcode_cblt' );

}
if ( ! function_exists( 'jplt_shortcode_cblt' ) ) {
	function jplt_shortcode_cblt( $atts ) {
	
	    $r = '';
	    extract( shortcode_atts( array( 'id' => '', ), $atts ) );

	    if ( isset( $atts['id'] ) && ! empty( $atts['id'] ) && '' != $atts['id'] ) {

	    	$paged = 1;
	    	$search_value = '';
	    	$tblid = $atts['id'];
				if( ! isset( $JpltTable )  ){
					$JpltTable = new JpltTable( );
				}
				$JpltTable->set_up_data( $tblid, $paged, $search_value );
				echo $JpltTable->get_the_table();

		     //var_dump( get_post_meta( $tblid, 'jplt_meta', true ) );
		   //   echo '<pre>';
		   //   var_dump( the_post()); 
				 // echo '</pre>';



	     die();

          $r .= '<div class="product-list-wrp"><span style="display:none" class="tblid">'.$tblid.'</span><form id="jplt-search"><input type="text" class="txt-search" placeholder="Search..." /><input type="submit" value="Search" id="submit-search" /></form><div class="loading"></div><div id="jplt-results"></div></div>';
	    
	    } else {
	      
	      $r .= 'The Id part is missing in your shortcode. Please check and try again.';
	    
	    }
	    
	    echo $r;
	}
}

add_action( 'wp_ajax_nopriv_jplt_display_table_ajax', 'jplt_display_table_ajax_cblk5' );

if ( ! function_exists( 'jplt_display_table_ajax_cblk5' ) ) {
   function jplt_display_table_ajax_cblk5() {

		check_ajax_referer( 'sfdsdf54sd5f4sdf89egtn', 'security' );
		
		$query_data = $_GET;
		$search_value = ($query_data['search']) ? $query_data['search'] : false;
		$paged = (isset($query_data['paged']) ) ? intval($query_data['paged']) : 1;
		$id = (isset($query_data['id']) ) ? $query_data['id'] : false;

		if ( $id ) {
			$r = '';
			if( !$jplt_table_class ){
				$jplt_table_class = new JpltTable( );
			}
			$jplt_table_class->set_up_data( $id, $paged, $search_value );
			//$r .=  $jplt_table_class->get_the_table( );
		} else{
		 $r .= 'Id is missing in your ajax call.';
		}
		echo $r;
    die();
  }
}


if ( is_admin() ) require_once( plugin_dir_path( __FILE__ ) .  '/includes/admin/jplt/jplt-post-type.php' );
if ( is_admin() ) require_once( plugin_dir_path( __FILE__ ) . '/includes/admin/jplt/jplt-admin-init.php' );
if ( is_admin() ) require_once( plugin_dir_path( __FILE__ ) .  '/includes/admin/jplt/jplt-admin-meta.php' );
