<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( );
}
if ( ! class_exists( 'JpltAdminMeta' ) ) {
/**
 * JpltAdminMeta Class
 *
 * @category Class
 * @package  Jplt
 * @author    Jiger Patel
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.jigerpatel.co.uk/
 * @since 0.0.1
 */
  class JpltAdminMeta
  {
    //variables
    private $metapageslug = 'jplt_template';
    private $meta_col_name = 'jplt_meta';
    private $meta_property_name = 'jplt_property_meta';    
    private $current_post_id;
    private $loc_metadata;
    private $loc_property_meta;     
    private $column_count = 0;
    private $property_count = 0;       

    private $metadata = array();
    private $plugin_url;

   
 
    /**
     * [$column_type_options List of all the woocommerce product fields that this plugin currently supports]
     *
     * @var array
     * @since 0.0.1
     */
    private $column_type_options = array(
        'name'                => 'Product Name',
        'description'         => 'Product Description',
        'shortdescription'    => 'Product Short Description',
        'price'               => 'Price',
        'cart_button'         => 'Add to cart Button',
        'sku'                 => 'SKU',
        'category'            => 'Product Category',
        'tag'                 => 'Tags',
        'stock'               => 'Stock',
        'stock_status'        => 'Stock Status',
        'stock_qty'           => 'Stock Quantity',
        'weight'              => 'weight',
        'dimension'           => 'Dimensions (L x W x H)',
        'dimension_width'     => 'Dimensions (W)',
        'dimension_height'    => 'Dimensions (H)',
        'shipping_class'      => 'Shipping class',
        'attribute'           => 'Attribute',
        'featured_image'      => 'Feature Image',
        );
    /**
     * [__construct Load up meta actions and check for current page post type]
     */
    public function __construct() {
        JpltAdminInit::j_print( 'JpltAdminMeta __construct actions added' );
        //set the vars
        $this->plugin_url = plugins_url();
        $this->current_post_id = get_the_ID();

        add_action( 'current_screen', array( $this, 'is_post_page_jp' ) );

        //ajax add and remove column actions
        add_action( 'wp_ajax_jplt_get_col_fields_ajax', array( $this, 'jplt_get_col_fields_ajax_cblt' ) );
        add_action( 'wp_ajax_jplt_remove_col_fields_ajax', array( $this, 'jplt_remove_col_fields_ajax_cblt' ) );
        add_action( 'save_post', array( $this, 'jplt_save_postdata' ), 10, 2 );
    }
    public function is_post_page_jp( $current_screen ) {

      if ( is_admin() && 'post' == $current_screen->base && 'jplt_template' == $current_screen->id && 'jplt_template' && $current_screen->post_type ) {
          $this->add_c_meta_box();
          $this->jplt_load_scripts();
      } else {
          JpltAdminInit::j_print( 'false' );
          return false;
      }
    }
    /**
     * [jplt_save_postdata description]
     * @param  [type] $post_id
     * @return [type]          Save the jplt_meta array
     */
    public function jplt_save_postdata( $post_id ) {
      if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
          return;
      }
        $j = $_POST['jplt_meta'];
        $k = $_POST['jplt_property_meta'];
        update_post_meta( $post_id, 'jplt_meta', $j );
        update_post_meta( $post_id, 'jplt_property_meta', $k );
    }
    public static function jplt_load_scripts() {
        JpltAdminInit::j_print( 'Scripts Loaded' );
        $plugin_url = plugins_url();
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_style( 'tableviewcss', $plugin_url . '/jplt/assets/css/jpltadmincss.css' );
        wp_enqueue_script( 'jpltadminjs', $plugin_url . '/jplt/assets/js/adminJs.js',array( 'jquery', 'wp-color-picker' ), false, true );
        // wp_enqueue_script( 'tableviewjs', $plugin_url . '/jplt/assets/js/jplt.js',array( 'jquery' ), '', true );
        wp_localize_script( 'jpltadminjs', 'jpb_ajax_params', array(
              'ajax_url' => admin_url( 'admin-ajax.php' ),
              'security' => wp_create_nonce( 'jk3489dj3bgd9k' ),
        ) );
        // wp_enqueue_script( 'tableviewjs' );
    }
    /**
     * [add_c_meta_box add the metabox with the render hook.]
     */
    public function add_c_meta_box() {
      add_meta_box( 'jplt-section', __( 'Generate your table', 'JPLT_TEXTDOMAN' ), array( $this, 'render_meta_box' ), $this->metapageslug, 'normal' );
    }
    /**
     * [render_meta_box generate the metabox and render it.]
     *
     * @param [type] $post ...
     * echo [html] Returs the full metabox.
     */
    public function render_meta_box( $post ) {
        //set the metadata
        $this->current_post_id = get_the_ID();
        $this->loc_metadata = get_post_meta( $this->current_post_id, $this->meta_col_name, true );
        $this->loc_property_meta = get_post_meta( $this->current_post_id, $this->meta_property_name, true );
        $response = '';
        //get the headder part of the custom metabox 
        $response .= $this->get_meta_box_head_part();
        //get the columns tab options
        $response .= $this->get_table_column_options( /*$loc_metadata, $this->current_post_id,  $this->column_count*/ );
        $response .= $this->get_table_property_options( $this->property_count,  $this->loc_property_meta );        
        $response .= $this->get_meta_box_foot_part( $this->column_count );
        echo $response;
    }

    private function get_meta_box_head_part() {
        return '
        <div id="jplt-meta-wrap">
        <div id="jplt-tabs">
          <ul>
            <li class="tab-link current" data-tab="tab-1">Table Columns</li>
            <li class="tab-link" data-tab="tab-2">Table Properties</li>
          </ul>';
    }

    private function get_meta_box_foot_part() {
      return '
      </div>
      <input id="foot-submit" name="save" type="submit" class="button button-primary button-large" id="publish" value="Save your Columns">
      </div>';
    }

    private function get_table_column_options() {

      $r = '<div id="tab-1" class="tab-content current">';

      if ( count( $this->loc_metadata ) > 0 && is_array( $this->loc_metadata ) ) {
        foreach ( $this->loc_metadata as $jplt_item ) {
          $trclass = '';
          if ( 1 == $this->column_count % 2 ) {
              $trclass = 'alt';
          }
          if ( 0 == $this->column_count ) {
            $this->column_count++;
          }
          $r .= '
            <div class="col '.esc_attr( $trclass ).'">
            <div class="col-title">
            <strong>Column '.esc_html( $this->column_count ).'</strong>
            <a href="#" id="jplt-remove" data-postid="'.esc_attr( $this->current_post_id ).'" data-countrow="'.esc_attr( $this->column_count ).'"  title="Remove the column">delete
            </a>
            </div>';
          $r .= '<div class="col-type">';
          $r .= $this->get_product_fields( $this->column_count, $jplt_item['jplt_c_selected'], $this->column_type_options );
          $r .= $this->get_hidden_fields( $this->column_count, $jplt_item );
          $r .= '</div>';
          $r .= '<div class="col-name">';
          $r .= $this->get_field_name( $this->column_count, $jplt_item['jplt_c_name'] );
          $r .= '</div></div>';
          $this->column_count++;
        }
      }
      $r .= '</div><a href="#" id="jplt-add-new" data-countrow="'.esc_attr( $this->column_count ).'" class="button button-primary button-large" title"Add new column">Add new column</a> ';
        return $r;
    }
    private function get_hidden_fields( $count, $value_array ) {
      $attributes = $this->get_attributes();
      $jplt_c_desc_lenght = ( isset( $value_array['jplt_c_desc_lenght'] ) && ! empty( $value_array['jplt_c_desc_lenght'] ) ? $value_array['jplt_c_desc_lenght'] : '');
      $jplt_c_show_sale = ( isset( $value_array['jplt_c_show_sale'] ) && ! empty( $value_array['jplt_c_show_sale'] ) ? $value_array['jplt_c_show_sale'] : '');
      $jplt_c_attribute = ( isset( $value_array['jplt_c_attribute'] ) && ! empty( $value_array['jplt_c_attribute'] ) ? $value_array['jplt_c_attribute'] : '');

      $r = '<div class="hiddenfields">
            <p class="length">Length of the descripton: 
            <input type="number" name="jplt_meta['.esc_attr( $count ).'][jplt_c_desc_lenght]" value="'.esc_attr( $jplt_c_desc_lenght ).'" id="jplt-c-desc-lenght" class="jplt-c-desc-lenght" placeholder="Length of the descripton"></p>
            <p class="ssp">Show the sale price? <input  placeholder="Show sale price" type="checkbox" name="jplt_meta['.esc_attr( $count ).'][jplt_c_show_sale]" value="show-sale" '.( 'show-sale' == $jplt_c_show_sale ? 'checked' : '') .' id="jplt-c-show-sale">
            </p>
            <p class="att">Select a attribute
            <select placeholder="Select a attribute"  class="" name="jplt_meta['.esc_attr( $count ).'][jplt_c_attribute]">
            <option value="">Select a attribute</option>';
      foreach ( $attributes as $key => $value ) :
          $r .= '<option value="'.esc_attr( $key ).'"'.( $key == $jplt_c_attribute ? 'selected' : '' ).'>'.esc_html( $value ).'</option>';
      endforeach;
      $r .= '</select></p></div>';
      return $r;
    }
    private function get_attributes() {
      $attribute_array = array();
      $attribute_taxonomies = wc_get_attribute_taxonomies();
      if ( $attribute_taxonomies ) {
        foreach ( $attribute_taxonomies as $tax ) {
          if ( taxonomy_exists( wc_attribute_taxonomy_name( $tax->attribute_name ) ) ) {
            $attribute_array[ $tax->attribute_name ] = $tax->attribute_name;
          }
        }
      }
      return $attribute_array;
    }
    private function get_field_name( $count, $pvalue ) {
      return '
      <strong>Column name:</strong> 
      <input  placeholder="Column Name"  type="text" name="jplt_meta['.esc_attr( $count ).'][jplt_c_name]" value="'.esc_attr( $pvalue ).'" id="jplt_c_name">';
    }    
    private function get_product_fields( $c, $pvalue, $options ) {
      $r = '';
      if ( ! empty( $options ) ) {
        $r .= '<strong>Select column type:</strong><select id="jplt-on-select" name="jplt_meta['.$c.'][jplt_c_selected]" >';
        foreach ( $options as $key => $value ) {
            $r .= '<option value="'. esc_attr( $value ) .'" '. ( $value == $pvalue ? ' selected' : '' ) .'>'. esc_html( $value ) .'</option>';
        }
        $r .= '</select>';
      }
      return $r;
    }
    private function get_table_property_options( $c, $loc_property_meta ) {
      $r = ' 
      <div id="tab-2" class="tab-content">
      <div class="meta-property-fields">
      <h3>Table Properties </h3>';
      $r .= '<p>Rows per page Max-300: <input type="number" name="jplt_property_meta['.esc_attr( $c ).'][jplt_rows_per_page]" value="'. esc_attr( $loc_property_meta[0]['jplt_rows_per_page'] ).'" id="jplt-c-desc-rows-per-page" class="jplt-c-rows-per-page" placeholder="How many rows per page?"></p>';
      $r .= '<h4>Table styles </h4>';
      $r .= '<p>Table Font Size: <input type="text" name="jplt_property_meta['.esc_attr( $c ).'][jplt_tb_font_size]" value="'. esc_attr( $loc_property_meta[0]['jplt_tb_font_size'] ) .'" class="jplt-tb-font-size" /></p>';
      $r .= '<p>Table Font Family: <input type="text" name="jplt_property_meta['.esc_attr( $c ).'][jplt_tb_font_family]" value="'. esc_attr(  $loc_property_meta[0]['jplt_tb_font_family'] ).'" class="jplt-tb-font-family" /></p>';
      $r .= '<p>Table Font Color: <input type="text" name="jplt_property_meta['.esc_attr( $c ).'][jplt_tb_font_color]" value="'. esc_attr( $loc_property_meta[0]['jplt_tb_font_color'] ) .'" class="jplt-tb-font-color jpltcolorpic" data-default-color="#effeff" /></p>';
      $r .= '<p>Table Font Weight: <input type="text" name="jplt_property_meta['.esc_attr( $c ).'][jplt_tb_font_weight]" value="'. esc_attr( $loc_property_meta[0]['jplt_tb_font_weight'] ).'" class="jplt-tb-font-weight" /></p>';
      $r .= '<h4>Table headder styles </h4>';
      $r .= '<p>Table Headder Font Size: <input type="text" name="jplt_property_meta['.esc_attr( $c ).'][jplt_th_font_size]" value="'. esc_attr( $loc_property_meta[0]['jplt_th_font_size'] ) .'" class="jplt-th-font-size" /></p>';
      $r .= '<p>Table Headder Font Color: <input type="text" name="jplt_property_meta['.esc_attr( $c ).'][jplt_th_font_color]" value="'.esc_attr(  $loc_property_meta[0]['jplt_th_font_color'] ).'" class="jplt-th-font-color" /></p>';
      $r .= '<p>Table Headder Font Weight: <input type="text" name="jplt_property_meta['.esc_attr( $c ).'][jplt_th_font_weight]" value="'.esc_attr(  $loc_property_meta[0]['jplt_th_font_weight'] ).'" class="jplt-th-font-weight" /></p>';
      $r .= '<p>Table Headder Border Color: <input type="text" name="jplt_property_meta['.esc_attr( $c ).'][jplt_th_border_color]" value="'.esc_attr(  $loc_property_meta[0]['jplt_th_border_color'] ).'" class="jplt-th-border-color jpltcolorpic" data-default-color="#effeff" /></p>';
      $r .= '<p>Table Headder Border Bottom: <input type="text" name="jplt_property_meta['.esc_attr( $c ).'][jplt_th_border_bottom]" value="'.esc_attr( $loc_property_meta[0]['jplt_th_border_bottom'] ).'" class="jplt-th-border-bottom" /></p>';
      $r .= '<p>Table Headder Border Top: <input type="text" name="jplt_property_meta['.esc_attr( $c ).'][jplt_th_border_top]" value="'.esc_attr(  $loc_property_meta[0]['jplt_th_border_top'] ).'" class="jplt-th-border-top" /></p>';  
      $r .= '<p>Table Headder Border Left: <input type="text" name="jplt_property_meta['.esc_attr( $c ).'][jplt_th_border_left]" value="'.esc_attr(  $loc_property_meta[0]['jplt_th_border_left'] ).'" class="jplt-th-border-left" /></p>';
      $r .= '<p>Table Headder Border Right: <input type="text" name="jplt_property_meta['.esc_attr( $c ).'][jplt_th_border_right]" value="'.esc_attr(  $loc_property_meta[0]['jplt_th_border_right'] ).'" class="jplt-th-border-right" /></p>';
      $r .= '<h4>Table row styles </h4>';
      $r .= '<p>Table Rows Border Color: <input type="text" name="jplt_property_meta['.esc_attr( $c ).'][jplt_tr_border_color]" value="'.esc_attr(  $loc_property_meta[0]['jplt_tr_border_color'] ).'" class="jplt-tr-border-color jpltcolorpic" data-default-color="#effeff" /></p>';
      $r .= '<p>Table Rows Border Bottom: <input type="text" name="jplt_property_meta['.esc_attr( $c ).'][jplt_tr_border_bottom]" value="'.esc_attr(  $loc_property_meta[0]['jplt_tr_border_bottom'] ).'" class="jplt-tr-border-bottom" /></p>';
      $r .= '<p>Table Rows Border Top: <input type="text" name="jplt_property_meta['.esc_attr( $c ).'][jplt_tr_border_top]" value="'.esc_attr(  $loc_property_meta[0]['jplt_tr_border_top'] ).'" class="jplt-tr-border-top" /></p>'; 
      $r .= '<p>Table Rows Border Left: <input type="text" name="jplt_property_meta['.esc_attr( $c ).'][jplt_tr_border_left]" value="'.esc_attr(  $loc_property_meta[0]['jplt_tr_border_left'] ).'" class="jplt-tr-border-left" /></p>';
      $r .= '<p>Table Rows Border Right: <input type="text" name="jplt_property_meta['.esc_attr( $c ).'][jplt_tr_border_right]" value="'.esc_attr(  $loc_property_meta[0]['jplt_tr_border_right'] ).'" class="jplt-tr-border-right" /></p>';               
                
      $r .= '</div></div>';
      return $r;
    }

    public function jplt_get_col_fields_ajax_cblt() {
      $c = $_REQUEST['countq'];
      $r = '';
      $trclass = '';
      if ( 0 == $c % 2 ) {
          $trclass = 'alt';
      }
      if ( '' == $c ) {
          $r .= 'Please refresh and try again!';
      } else {
        $r .= '<div class="col '.$trclass.'"><div class="col-title"><strong>Column '.$c.'</strong></div>';
        $r .= '<div class="col-type">'.$this->get_product_fields( $c, '', $this->column_type_options );
        $r .= $this->get_hidden_fields( $c, '' );
        $r .= '</div>';
        $r .= '<div class="col-name">';
        $r .= $this->get_field_name( $c, '' );
        $r .= '</div>';
      }
        echo $r;
        die();
    }
    public function jplt_remove_col_fields_ajax_cblt() {
      $c = $_REQUEST['colId'];
      $p = $_REQUEST['pstId'];
      $s = $_REQUEST['security'];
     // $m = $this->get_metadata( $p );
      $c--;
      //die();
      array_splice( $m, $c, 1 );
      update_post_meta( $p, 'jplt_meta', $m );
      $r = $this->get_table_column_options( $m,  $p );
      echo $r;
      die();
    }
  }
}
new JpltAdminMeta();
