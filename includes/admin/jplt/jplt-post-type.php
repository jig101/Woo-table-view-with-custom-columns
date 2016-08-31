<?php
/**
 * Register a Display product post type.
 */
function jplt_template_init() {

	$labels = array(
		'name'               => __( 'Display product in table', 'jplt' ),
		'singular_name'      => __( 'Table Templates Editor', 'jplt' ),
		'menu_name'          => __( 'Table Generator', 'jplt' ),
		'name_admin_bar'     => __( 'Table Templates Editor', 'jplt' ),
		'add_new'            => __( 'Add New', 'jplt' ),
		'add_new_item'       => __( 'Add New Table Template', 'jplt' ),
		'new_item'           => __( 'New Table Template', 'jplt' ),
		'edit_item'          => __( 'Edit Table Template', 'jplt' ),
		'view_item'          => __( 'View Table Template', 'jplt' ),
		'all_items'          => __( 'All Table Template', 'jplt' ),
		'search_items'       => __( 'Search Table Template', 'jplt' ),
		'parent_item_colon'  => __( 'Parent Table Template:', 'jplt' ),
		'not_found'          => __( 'No Tables found.', 'jplt' ),
		'not_found_in_trash' => __( 'No Tables found in Trash.', 'jplt' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => false,
		'rewrite'            => array( 'slug' => 'jplt_template' ),
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => 110,
		'menu_icon'          => plugins_url().'/jplt/assets/images/squares.png',
		'supports'           => array( 'title' ),
	);

	register_post_type( 'jplt_template', $args );
}
add_action( 'init', 'jplt_template_init' );

/*
 Force a single column layout in screen layout for the custom post type jplt_template
 */
function jplt_screen_layout_columns( $columns ) {
	$columns['jplt_template'] = 1;
	return $columns;
}
add_filter( 'screen_layout_columns', 'jplt_screen_layout_columns' );

function jplt_screen_layout_jplt_template() {
	return 1;
}
add_filter( 'get_user_option_screen_layout_jplt_template', 'jplt_screen_layout_jplt_template' );
add_filter( 'manage_jplt_template_posts_columns', 'jplt_shortcode_head', 10 );
add_action( 'manage_jplt_template_posts_custom_column', 'jplt_shortcode_content', 10, 2 );
add_filter( 'manage_jplt_template_posts_columns', 'jplt_columns_remove_date' );
// CREATE TWO FUNCTIONS TO HANDLE THE COLUMN
function jplt_shortcode_head( $defaults ) {
	$defaults['jplt_shortcode'] = 'Shortcode';
	return $defaults;
}
function jplt_shortcode_content( $column_name, $post_id ) {
	if ( 'jplt_shortcode' == $column_name  ) {
		echo '[JPDisplayTable id="'.$post_id .'"]';
	}
}
// REMOVE DEFAULT CATEGORY COLUMN
function jplt_columns_remove_date( $defaults ) {
	unset( $defaults['date'] );
	return $defaults;
}
