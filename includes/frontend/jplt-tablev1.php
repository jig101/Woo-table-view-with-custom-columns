<?php
if ( ! defined( 'ABSPATH' ) ) {
		die( );
}
if ( ! class_exists( 'JpltTable' ) ) {
/**
 * JpltAdminMeta Class
 *
 * @category Class
 * @package  Jplt_Frontend_Helper
 * @author    Jiger Patel
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.jigerpatel.co.uk/
 * @since 0.0.1
 */
	class JpltTable
	{
		private $post_id;
		private $query_args;
		private $table_column_array;
		private $table_property_array;
		private $query_statment;
    private $add_to_cart_option = false;
    private $add_to_cart_option_array_key_reff;          

		private $table_col_meta_name = 'jplt_meta';
		private $tabel_property_meta_name = 'jplt_property_meta';		
		private $cat_array = array();
		private $tag_array = array();
		private $post_per_page;
		private $results_array = array();
		private $col_count = 0;		
	




		public function __construct( ) {
			//echo "class init";
			Jplt_Frontend_Helper::j_print('JpltTable  cons');
		}

		public function set_the_id( $jplt_post_id ){
			$this->post_id = $jplt_post_id; 
			$this->setup_requierd_query_data();
		}

		private function setup_requierd_query_data( ){
			if( $this->post_id ) {
				if( ! isset( $this->table_col_array ) && empty( $this->table_col_array ) ) {
					$this->table_col_array = get_post_meta( $this->post_id, $this->table_col_meta_name, true );
				}
				
				if( ! isset( $this->table_property_array ) && empty( $this->table_property_array ) ) {
					$this->table_property_array  = get_post_meta( $this->post_id, $this->tabel_property_meta_name, true );
				}
				
				if( ! isset( $this->post_per_page ) && empty( $this->post_per_page ) ) {
				$this->post_per_page = $this->table_property_array[0][0]['jplt_rows_per_page'];
				}
			}
		}
    
    public function get_the_table(){

      //Get selected column data from db as an array
      $this->results_array = $this->get_selected_column_results(); 
      //Get the results 

    }

// SELECT DISTINCT
// post_title
// , post_content
// , ".$dbprefix."postmeta.meta_value AS Price
// ,(SELECT ".$dbprefix."terms.name
//     FROM ".$dbprefix."terms
//     INNER JOIN ".$dbprefix."term_taxonomy on ".$dbprefix."terms.term_id = ".$dbprefix."term_taxonomy.term_id
//     INNER JOIN ".$dbprefix."term_relationships wpr on wpr.term_taxonomy_id = ".$dbprefix."term_taxonomy.term_taxonomy_id
//     WHERE taxonomy= 'pa_wine' and ".$dbprefix."posts.ID = wpr.object_id
// ) AS "wine"
// FROM ".$dbprefix."posts
// LEFT JOIN ".$dbprefix."postmeta ON ".$dbprefix."posts.ID = ".$dbprefix."postmeta.post_id AND '_regular_price' = ".$dbprefix."postmeta.meta_key
// WHERE post_type = 'product' and ".$dbprefix."posts.post_status = 'publish' 
// ORDER BY
// wine

		public function get_selected_column_results( ) {
			global $wpdb;
			$dbprefix = $wpdb->prefix;
      $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
      $post_per_page = 20;
      $offset = ($paged - 1)*$post_per_page;
      $active_cols = array();


      $query  = "SELECT DISTINCT ID ";
      
      //query statments for all the diffrent availbel coulmn fields
      $c = 1;
      foreach ( $this->table_col_array as $table_item ) {
      		switch ( $table_item['jplt_c_selected'] ) {
          case 'Product Name':
            //Jplt_Frontend_Helper::j_print( 'Product Name' ); post_title ,post_content 
            $query  .= ",post_title ";          
            $c++; 
            break;
          case 'Product Description':
              //Jplt_Frontend_Helper::j_print( 'Product Description' );
            $query  .= ",post_content ";              
            $c++; 
            break;
          case 'Product Short Description':
              //Jplt_Frontend_Helper::j_print( 'Product Short Description' );
            $query  .= ",post_excerpt "; 
            $c++; 
            break;      			
      			case 'Price':
							$query  .= ",(SELECT ".$dbprefix."postmeta.meta_value 
													FROM   ".$dbprefix."postmeta 
                          WHERE  ".$dbprefix."posts.id = ".$dbprefix."postmeta.post_id 
                          AND '_price' = ".$dbprefix."postmeta.meta_key ) AS 'price' ";
						$c++; 
            break;
						case 'Attribute':
							$query .= ",(SELECT ".$dbprefix."terms.name
									    FROM ".$dbprefix."terms
									    INNER JOIN ".$dbprefix."term_taxonomy on ".$dbprefix."terms.term_id = ".$dbprefix."term_taxonomy.term_id
									    INNER JOIN ".$dbprefix."term_relationships wpr on wpr.term_taxonomy_id = ".$dbprefix."term_taxonomy.term_taxonomy_id
									    WHERE taxonomy = 'pa_".strtolower($table_item['jplt_c_attribute'])."' and ".$dbprefix."posts.ID = wpr.object_id
									) AS '".strtolower($table_item['jplt_c_attribute'])."' ";
						$c++; 
            break;
						case 'SKU':
						$query  .= ",(SELECT ".$dbprefix."postmeta.meta_value 
													FROM   ".$dbprefix."postmeta 
                          WHERE  ".$dbprefix."posts.id = ".$dbprefix."postmeta.post_id 
                          AND '_sku' = ".$dbprefix."postmeta.meta_key ) AS 'sku' ";
						$c++; 
            break;
						case 'Stock Status':
						$query  .= ",(SELECT ".$dbprefix."postmeta.meta_value 
													FROM   ".$dbprefix."postmeta 
                          WHERE  ".$dbprefix."posts.id = ".$dbprefix."postmeta.post_id 
                          AND '_stock_status' = ".$dbprefix."postmeta.meta_key ) AS 'stock_status' ";
						$c++; 
            break;
						case 'Stock Quantity':
						$query  .= ",(SELECT ".$dbprefix."postmeta.meta_value 
													FROM   ".$dbprefix."postmeta 
                          WHERE  ".$dbprefix."posts.id = ".$dbprefix."postmeta.post_id 
                          AND '_stock' = ".$dbprefix."postmeta.meta_key ) AS 'stock' ";
						$c++; 
            break;	
						case 'weight':
						$query  .= ",(SELECT ".$dbprefix."postmeta.meta_value 
													FROM   ".$dbprefix."postmeta 
                          WHERE  ".$dbprefix."posts.id = ".$dbprefix."postmeta.post_id 
                          AND '_weight' = ".$dbprefix."postmeta.meta_key ) AS 'weight' ";		
						$c++; 
            break;	
						case 'Dimensions (L)':
						$query  .= ",(SELECT ".$dbprefix."postmeta.meta_value 
													FROM   ".$dbprefix."postmeta 
                          WHERE  ".$dbprefix."posts.id = ".$dbprefix."postmeta.post_id 
                          AND '_length' = ".$dbprefix."postmeta.meta_key ) AS 'length' ";
						$c++; 
            break;	
						case 'Dimensions (W)':
						$query  .= ",(SELECT ".$dbprefix."postmeta.meta_value 
													FROM   ".$dbprefix."postmeta 
                          WHERE  ".$dbprefix."posts.id = ".$dbprefix."postmeta.post_id 
                          AND '_width' = ".$dbprefix."postmeta.meta_key ) AS 'width' ";
						$c++; 
            break;	
						case 'Dimensions (H)':
						$query  .= ",(SELECT ".$dbprefix."postmeta.meta_value 
													FROM   ".$dbprefix."postmeta 
                          WHERE  ".$dbprefix."posts.id = ".$dbprefix."postmeta.post_id 
                          AND '_height' = ".$dbprefix."postmeta.meta_key ) AS 'height' ";	
						$c++; 
            break;
            case 'Add to cart Button':
              $this->add_to_cart_option = true;
              $this->add_to_cart_option_array_key_reff = $c;
            $c++; 
            break;              
						default:
            case 'Add to cart Button':
 
            $c++; 
            break;              
            default:            
						# no rows send empty...
						$c++; 
            break;
					}	
      }

      $query  .= "FROM ".$dbprefix."posts ";
			
			foreach ( $active_cols as $current_col ) {
				$query  .= "LEFT JOIN ".$dbprefix."postmeta ON ".$dbprefix."posts.ID = ".$dbprefix."postmeta.post_id AND '".$current_col."' = ".$dbprefix."postmeta.meta_key ";
			}
			$query  .= "WHERE post_type = 'product' and ".$dbprefix."posts.post_status = 'publish' ";
			$query  .= "ORDER BY post_title ";
      $query  .= "LIMIT ".$offset.", ".$post_per_page." ";

      $sql_result = $wpdb->get_results( $query , OBJECT);
      
      //check if add to cart option is enabled.
      foreach ( $sql_result as $sql_result_item ) { 
        if( $this->add_to_cart_option && $this->add_to_cart_option_array_key_reff != null ){
          echo "<pre>";
          var_dump($this->add_to_cart_option);
          var_dump($this->add_to_cart_option_array_key_reff);
          var_dump($this->table_col_array[$this->add_to_cart_option_array_key_reff]);
          var_dump($sql_result_item);
          var_dump( get_home_url() . "/?add-to-cart=3432");
          echo "<a";
          echo "</pre>";
          die();
        }
      }    


		}

    private function get_query_results ( ) {
      
      
    }

	}//end
}
