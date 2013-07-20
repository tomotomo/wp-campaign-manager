<?php
/*
Plugin Name: WP Campaign Manager
Plugin URI: 
Description: 
Version: 0.1
Author: Tomoyuki Sugita
Author URI: http://tomotomosnippet.blogspot.jp/
License: GPLv2 or later
License URI: 
*/

function wcm_init() {
  $labels = array(
    'name' => __('Campaign'),
    'singular_name' => __('Campaign'),
    'add_new' => __('Add New'),
    'add_new_item' => __('Add New Campaign'),
    'edit_item' => __('Edit Campaign'),
    'new_item' => __('New Campaign'),
    'all_items' => __('All Campaign'),
    'view_item' => __('View '),
    'search_items' => __('Search Campigns'),
    'not_found' =>  'No Campigns found',
    'not_found_in_trash' => __('No Campigns found in Trash'), 
    'parent_item_colon' => '',
    'menu_name' => __('Campigns')
  );

  $args = array(
    'labels' => $labels,
    'public' => false,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_menu' => true, 
    'query_var' => true,
    'rewrite' => array( 'slug' => __('book') ),
    'capability_type' => __('post'),
    'has_archive' => true, 
    'hierarchical' => false,
    'menu_position' => null,
    'supports' => array( 'title', 'editor', 'custom-fields', 'author', 'thumbnail', 'excerpt' )
  ); 

  register_post_type( 'wcm-campaign', $args );
}
add_action( 'init', 'wcm_init' );


// ショートコードの登録をしよう
add_shortcode('CTA', 'wcm_shortcode');
function wcm_shortcode ($atts)
{
	// ショートコードのオプションで取得する投稿を決める仕様
	$post_id = $atts['id'];
	$content = get_post($post_id);
	
	$code = $content->post_content;
	return $code;
}