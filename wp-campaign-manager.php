<?php
/*
Plugin Name: WP Campaign Manager
Plugin URI: 
Description: Provides Shortcodes to display and manage your Campaign banners.
Version: 0.1
Author: Tomoyuki Sugita
Author URI: http://tomotomosnippet.blogspot.jp/
License: GPLv2 or later
License URI: 
*/

// Define textdomain
define('WCM_TEXTDOMAIN', 'wcm');
//TODO Make wcm.po

// Initialize Custom post type.
add_action( 'init', 'wcm_init' );
function wcm_init() {
	
	// Set text-domain
	load_plugin_textdomain(WCM_TEXTDOMAIN, false, dirname(plugin_basename(__FILE__)).'/languages');
	
	
  $labels = array(
    'name' => __('Campaign', WCM_TEXTDOMAIN),
    'singular_name' => __('Campaign', WCM_TEXTDOMAIN),
    'add_new' => __('Add New', WCM_TEXTDOMAIN),
    'add_new_item' => __('Add New Campaign', WCM_TEXTDOMAIN),
    'edit_item' => __('Edit Campaign', WCM_TEXTDOMAIN),
    'new_item' => __('New Campaign', WCM_TEXTDOMAIN),
    'all_items' => __('All Campaign', WCM_TEXTDOMAIN),
    'view_item' => __('View Campaign'),
    'search_items' => __('Search Campaigns', WCM_TEXTDOMAIN),
    'not_found' =>  __('No Campaigns found', WCM_TEXTDOMAIN),
    'not_found_in_trash' => __('No Campaigns found in Trash', WCM_TEXTDOMAIN), 
    'parent_item_colon' => '',
    'menu_name' => __('Campaigns', WCM_TEXTDOMAIN)
  );

  $args = array(
    'labels' => $labels,
	'description' => __('Manage campaigns.', WCM_TEXTDOMAIN),
    'public' => false,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_menu' => true, 
    'query_var' => true,
	// TODO Create an original menu icon
	//'menu_icon' => ???,
    'rewrite' => array( 'slug' => 'campaign' ),
    'capability_type' => 'post',
    'has_archive' => true, 
    'hierarchical' => false,
    'menu_position' => null,
    'supports' => array( 'title', 'editor', 'custom-fields', 'author', 'thumbnail', 'excerpt' )
  ); 

  register_post_type( 'wcm-campaign', $args );
}


// Add shortcode [wcm-show id=post_id]
add_shortcode('wcm-show', 'wcm_shortcode');
function wcm_shortcode ($atts)
{

	// FIXME ? YOU can call all post type by this short code :P
	$post_id = $atts['id'];
	$content = get_post($post_id);
	$code = '';
	
	// Not found the post
	if (empty($content)) {
		return $code;
	}
	
	// Check post_status is publish
	if ($content->post_status === 'publish') {
		$code = $content->post_content;		
	}
	return $code;
}

// Add Button on post
add_action( 'media_buttons', 'wcm_buttons', 99 );
function wcm_buttons () {
	
	$active_list = wcm_get_campaigns();
	
	
	echo '<select name="hoge">';
	foreach ($active_list as $post) {
		echo '<option value="'. esc_attr(wcm_build_shortcode($post->ID)) .'">'.esc_html($post->post_title.'['.$post->post_status.']').'</option>';
	}
	echo '</select>';
	echo '<button onclick="javascript:alert(\'TODO:カーソル位置にキャンペーンのショートコードを追加\');return false;">追加</button>';
}
function wcm_get_campaigns ($arg=array()) {
	
	/**
	 * Understand post_status
	 * 
	 * 'publish' - a published post or page
	 * 'pending' - post is pending review
	 * 'draft' - a post in draft status
	 * 'auto-draft' - a newly created post, with no content
	 * 'future' - a post to publish in the future
	 * 'private' - not visible to users who are not logged in
	 * 'inherit' - a revision. see get_children.
	 * 'trash' - post is in trashbin. added with Version 2.9.  
	 */
	
	$arg = array(
		'post_type' => 'wcm-campaign',
		'post_status' => array('publish', 'pending', 'draft'),
	);
	$list = get_posts($arg);
	
	return $list;
}

function wcm_build_shortcode ($id)
{
	$shortcode = '[wcm-show id=%d]';
	$shortcode = sprintf($shortcode, (int)$id);
	return $shortcode;
}
