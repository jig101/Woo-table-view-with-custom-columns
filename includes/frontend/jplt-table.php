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
    /**
     * Vars 
     */
		private $post_id;
    private $paged;  
    private $search_value;
    private $table_col_meta_name = 'jplt_meta';
    private $tabel_property_meta_name = 'jplt_property_meta'; 
    private $table_column_array;
    private $table_property_array;
    private $data_setup = false;    
       
		private $query_args;
		private $query_statment;	
		private $cat_array = array();
		private $tag_array = array();
		private $post_per_page = 20;
		private $results_array = array();
		private $col_count = 0;		
	




		public function __construct( ) {
			Jplt_Frontend_Helper::j_print('JpltTable  cons');
		}

		public function set_up_data( $id, $paged, $search_value ){
			if( $id != null && $paged != null ){
        ( $this->post_id == $id ? : $this->post_id = $id );
        ( $this->paged == $paged ? : $this->paged = $paged );
        ( $this->search_value == $search_value ? : $this->search_value = esc_html( $search_value ) );
        $this->table_col_array = get_post_meta( $this->post_id, $this->table_col_meta_name, true );
        $this->table_property_array  = get_post_meta( $this->post_id, $this->tabel_property_meta_name, true );
        $this->post_per_page = ( is_numeric( $this->table_property_array[0]['jplt_rows_per_page'] ) && intval($this->table_property_array[0]['jplt_rows_per_page']) < 300 ? intval($this->table_property_array[0]['jplt_rows_per_page']) : 20 );
        if( empty( $this->table_col_array ) && empty( $this->table_property_array ) && empty( $this->paged ) && empty( $this->post_id ) ) {
          $this->data_setup = fales;
        } else {
          $this->data_setup = true;
        }
      }
		}
    //echo "<pre>";var_dump( $this->table_property_array );echo "</pre>";die();
    public function get_the_table( ){

      if( $this->data_setup ){

        $this->query_args = $this->setup_query_args();
        // echo "<pre>";var_dump( $this->query_args );echo "</pre>";die();

      }else{
        esc_html( 'There was a problem fetching the data please check the columns in admin area and try again dont forget to save your columns.' );
      }

// $current_page_transient = get_transient( 'jplt-main-tbl-trns' . $paged );

      // if( false === $current_page_transient ) {
        
        $this->query_args = $this->setup_query_args();
        $results = $this->get_query_results();
        $r = $this->get_table_outout($results );
        echo $r;
        echo "<pre>";var_dump( $r );echo "</pre>";die();






      // // }
      // // else{
      // //   esc_html( 'There was a problem fetching the data please check the columns in admin area and try again dont forget to save your columns.' );
      // }
      //   $args = $this->set_query_args($paged);

      //   $cpq_result = $this->get_query_results($args);

      //   $r .= $this->get_table_outout($cpq_result, $paged);
      //   $r .= $this->get_table_css();

      // //   set_transient( 'jplt-main-tbl-trns' . $paged , $current_page_query_result, 300 );
      // // }
      // echo $r;
    }

    private function setup_query_args(){

        $args = array( 
          'paged'          => $this->paged,
          'post_type'      => 'product',
          'fields'         => 'ids',
          'post_status'    => 'publish',
          'pos_per_page'    => $this->post_per_page,
       ); //
        return $args;
    }
    private function get_query_results( $args ) {
      $res = array();    
      $res['columns'] = $this->get_selected_columns( $this->table_col_array );
      $res['rows'] =  $this->get_selected_columns_rows();
      return $res;
    }

    private function get_selected_columns( $table_col_array ) {
      $r = '<div class="jplt-table-wrap"><table id="jplt-table"><thead><tr>';
      foreach ( $table_col_array as $table_item ) {
        $r .= '<th>';
        $r .= $table_item['jplt_c_name'];
        $r .= '</th>';
      }
      $r .= '</tr></thead>';
      return $r;
    }
    /**
     * [get_selected_columns description]
     * @param  [type] $table_col_array [description]
     * @return [type]                  [description]
     */
    private function get_selected_columns_rows( ) {
      
      //Make the curren page query.
      $query_r = new WP_Query( $this->query_args);
      // Jplt_Frontend_Helper::j_print( $query_r );
      // die();
      $row = array();

      if( $query_r->have_posts() ):
        while( $query_r->have_posts() ): $query_r->the_post();
          
          $theid = get_the_ID();
          // check what column and get data for the column
          $row[] = $this->get_row_field_data( $theid );

        endwhile;
      endif;

      //$r = array();

      return $row;
    }
    private function get_row_field_data( $theid ) {
      $c_product = 0;
      $c_product = new WC_Product($theid);
      $row = array();
      $desc_length = 120;
      $content_length = 120;
      $count = 0;

      $row['id'] = $theid;
      // //Jplt_Frontend_Helper::j_print( $table_col_array );
      // die();
      foreach (  $this->table_col_array as $selected_col ) {
        ////Jplt_Frontend_Helper::j_print( $selected_col );
        switch ( $selected_col['jplt_c_selected'] ) {
          case 'Product Name':
            //Jplt_Frontend_Helper::j_print( 'Product Name' );
            $row['name'] = get_the_title( );            
            break;
          case 'Product Description':
              //Jplt_Frontend_Helper::j_print( 'Product Description' );
              $j = get_the_content();
              $row['short_desc'] = Jplt_Frontend_Helper::the_excerpt_max_charlength( $content_length , $j);             
            break;
          case 'Product Short Description':
              //Jplt_Frontend_Helper::j_print( 'Product Short Description' );
              $j = get_the_excerpt();
              $row['short_desc'] = Jplt_Frontend_Helper::the_excerpt_max_charlength( $desc_length , $j);
            break;
          case 'Price':
              //Jplt_Frontend_Helper::j_print( 'Price' );
              $row['price'] = $c_product->get_price_html();
            break;
          case 'Add to cart Button':
              //Jplt_Frontend_Helper::j_print( 'Add to cart Button' ); 
            break;
          case 'SKU':
              //Jplt_Frontend_Helper::j_print( 'SKU' );
              $row['sku'] = $c_product->get_sku( );
            break;
          case 'Product Category':
              //Jplt_Frontend_Helper::j_print( 'Product Category' );
              $row['categories'] = $c_product->get_categories( );
            break;
          case 'Tags':
              //Jplt_Frontend_Helper::j_print( 'Tags' );
              $row['tags'] = $c_product->get_tags( );
            break;
          case 'Stock':
              //Jplt_Frontend_Helper::j_print( 'Stock' );
            break;
          case 'Stock Status':
              //Jplt_Frontend_Helper::j_print( 'Stock Status' );
            $row['stock_status'] = $c_product->stock_status;
            break;
          case 'Stock Quantity':
              //Jplt_Frontend_Helper::j_print( 'Stock Quantity' );
              $row['stock_qty'] = $c_product->stock;
            break;  
          case 'weight':
              //Jplt_Frontend_Helper::j_print( 'weight' );
              $row['weight'] = $c_product->weight;
            break;  
          case 'Dimensions (L x W x H)':
              //Jplt_Frontend_Helper::j_print( 'Dimensions (L x W x H)' );
              $row['length'] = $c_product->length;
            break;  
          case 'Dimensions (W)':
          $row['width'] = $c_product->width;
            break;  
          case 'Dimensions (H)':
            $row['height'] = $c_product->height;
            break;
          case 'Shipping class':
            $row['shipping_class'] =  $c_product->get_shipping_class();
            break;  
          case 'Attribute':
              //Jplt_Frontend_Helper::j_print( 'Attribute' );
              $c_att = $c_product->get_attribute( $selected_col['jplt_c_attribute'] );
              $row['Attribute'.$count] =  $c_att;
              $count++;
            break;  
          case 'Feature Image':
              $row['img'] = get_image( 'shop_thumbnail' );
            break;
          default:
            # no rows send empty...
            break;
          }

        }
        return $row;
    }
    private function get_table_css( ) {
      Jplt_Frontend_Helper::j_print(  $this->table_property_array );
        $r = '<style type="text/css">
        .jplt-table-wrap table#jplt-table{
            font-size: '.( ! $this->table_property_array[0]['jplt_tb_font_size'] ? 12 : $this->table_property_array[0]['jplt_tb_font_size'] ).';
            font-family: '.( ! $this->table_property_array[0]['jplt_tb_font_family'] ? inherit : $this->table_property_array[0]['jplt_tb_font_family'] ).';
            color: '.( ! $this->table_property_array[0]['jplt_tb_font_color'] ? inherit : $this->table_property_array[0]['jplt_tb_font_color'] ).';
            font-weight: '.( ! $this->table_property_array[0]['jplt_tb_font_weight'] ? inherit : $this->table_property_array[0]['jplt_tb_font_weight'] ).';

        }
        .jplt-table-wrap table#jplt-table th{
            font-size: '.( ! $this->table_property_array[0]['jplt_th_font_size'] ? inherit : $this->table_property_array[0]['jplt_th_font_size'] ).';
            color: '.( ! $this->table_property_array[0]['jplt_th_font_color'] ? inherit : $this->table_property_array[0]['jplt_th_font_color'] ).';
            font-weight: '.( ! $this->table_property_array[0]['jplt_th_font_weight'] ? inherit : $this->table_property_array[0]['jplt_th_font_weight'] ).';
            border-color: '.( ! $this->table_property_array[0]['jplt_th_border_color'] ? inherit : $this->table_property_array[0]['jplt_th_border_color'] ).';
            border-bottom: '.( ! $this->table_property_array[0]['jplt_th_border_bottom'] ? inherit : $this->table_property_array[0]['jplt_th_border_bottom'] ).';
            border-top: '.( ! $this->table_property_array[0]['jplt_th_border_top'] ? inherit : $this->table_property_array[0]['jplt_th_border_top'] ).';
            border-left: '.( ! $this->table_property_array[0]['jplt_th_border_left'] ? inherit : $this->table_property_array[0]['jplt_th_border_left'] ).';
            border-right: '.( ! $this->table_property_array[0]['jplt_th_border_right'] ? inherit : $this->table_property_array[0]['jplt_th_border_right'] ).';
        }
        .jplt-table-wrap table#jplt-table tr{
            border-color: '.( ! $this->table_property_array[0]['jplt_tr_border_color'] ? inherit : $this->table_property_array[0]['jplt_tr_border_color'] ).';
            border-right: '.( ! $this->table_property_array[0]['jplt_tr_border_right'] ? inherit : $this->table_property_array[0]['jplt_tr_border_right'] ).';
            border-top: '.( ! $this->table_property_array[0]['jplt_tr_border_top'] ? inherit : $this->table_property_array[0]['jplt_tr_border_top'] ).';
            border-left: '.( ! $this->table_property_array[0]['jplt_tr_border_left'] ? inherit : $this->table_property_array[0]['jplt_tr_border_left'] ).';
            border-bottom: '.( ! $this->table_property_array[0]['jplt_tr_border_bottom'] ? inherit : $this->table_property_array[0]['jplt_tr_border_bottom'] ).';
        }</style>';
    
        return $r;
    }
    private function get_table_outout( $cpq_result ){
      //echo "<pre>";var_dump( $cpq_result );echo "</pre>";die();
      //
      $r ='<tr>';
        foreach ( $cpq_result['rows'] as $td => $value ) {
          if($td != 'id'){
            $r .= '<td>';
            $r .= $value;
            $r .= '</td>';
          }
        }
      $r .='</tr>';
      $r .= '</tbody></table></div>';
      $r .= '<div class="jpb-list-navigation">';

        $big = 999999999;

      $r .= paginate_links( array(

            'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),

            'format' => '?paged=%#%',

            'current' => max( 1, $paged ),

            'end_size' => 5,

            'mid_size' => 3,

            'total' => 100

        ) );

      $r .=  '</div>';
      $r .=  '';
        return $r;
    }

	}//end
}
