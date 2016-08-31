<?php
if ( ! defined( 'ABSPATH' ) ) {
		die( );
}
if ( ! class_exists( 'Jplt_Query' ) ) {
/**
 * JpltAdminMeta Class
 *
 * @category Class
 * @package  Jplt_Query
 * @author    Jiger Patel
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.jigerpatel.co.uk/
 * @since 0.0.1
 */


	class Jplt_Query extends WP_Query
	{ 
 
    function __construct( $args = array(), $orderBy = 'post_title' ) {

        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

				$args = array_merge( $args, array(
          'paged'          => $paged,
          'post_type'      => 'product',
          'post_status'    => 'publish',
          'pos_per_page'    => $this->post_per_page,
        ) );

         add_filter( 'posts_fields', array( $this, 'posts_fields' ) );
        add_filter( 'posts_join', array( $this, 'posts_join' ) );
        add_filter( 'posts_where', array( $this, 'posts_where' ) );
        add_filter( 'posts_orderby', array( $this, 'posts_orderby' ) );

        parent::__construct( $args );

        // Make sure these filters don't affect any other queries
         remove_filter( 'posts_fields', array( $this, 'posts_fields' ) );
        remove_filter( 'posts_join', array( $this, 'posts_join' ) );
        remove_filter( 'posts_where', array( $this, 'posts_where' ) );
        remove_filter( 'posts_orderby', array( $this, 'posts_orderby' ) );

    }

    function posts_fields( $sql ) {
      global $wpdb;
      return $sql . ", $wpdb->terms.name AS 'book_category'";
    }

    function posts_join( $sql ) {
      global $wpdb;
      return $sql . "
        INNER JOIN $wpdb->term_taxonomy ON ($wpdb->terms.term_id = $wpdb->term_taxonomy.term_id) 
        INNER JOIN $wpdb->term_relationships ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) 
      ";
    }

    function posts_where( $sql ) {
      global $wpdb;
      return $sql . " AND $wpdb->term_taxonomy.taxonomy = 'pa_wine' AND  $wpdb->posts.ID = $wpdb->term_relationships.object_id ";
    }

    function posts_orderby( $sql ) {
      global $wpdb;
      return "$wpdb->terms.name ASC, $wpdb->posts.post_title ASC";
    }
   

	}
}
