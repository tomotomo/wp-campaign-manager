<?php
/*
Plugin Name: WP Campaign Manager
Plugin URI: http://wordpress.org/plugins/wp-campaign-manager/
Description: Provides Shortcodes to display and manage your Campaign banners.
Version: 0.1.1
Author: Tomoyuki Sugita
Author URI: http://tomotomosnippet.blogspot.jp/
License: GPLv2 or later
*/

new WPCampaignManager();

class WPCampaignManager {
	/**
	 * plugin text domain 
	 */
	public $textdomain = 'wcm';
	public $post_type = 'wcm-campaign';

	public function __construct() {

		// Initialize Custom post type.
		add_action('init', array($this, 'init'));
		// Add Button on post
//		add_action('media_buttons', array($this, 'mce_buttons'), 99);

		// Add shortcode [wcm-show id=post_id]
		add_shortcode('wcm-show', array($this, 'make_shortcode'));
		
		$this->show_tags_on_posts();
	}

	/**
	 * Used while construct 
	 */
	public function init() {

		// Set text-domain
		load_plugin_textdomain($this->textdomain, false, dirname(plugin_basename(__FILE__)) . '/languages');


		$labels = array(
			'name' => __('Campaign', $this->textdomain),
			'singular_name' => __('Campaign', $this->textdomain),
			'add_new' => __('Add New', $this->textdomain),
			'add_new_item' => __('Add New Campaign', $this->textdomain),
			'edit_item' => __('Edit Campaign', $this->textdomain),
			'new_item' => __('New Campaign', $this->textdomain),
			'all_items' => __('All Campaign', $this->textdomain),
			'view_item' => __('View Campaign'),
			'search_items' => __('Search Campaigns', $this->textdomain),
			'not_found' => __('No Campaigns found', $this->textdomain),
			'not_found_in_trash' => __('No Campaigns found in Trash', $this->textdomain),
			'parent_item_colon' => '',
			'menu_name' => __('Campaigns', $this->textdomain)
		);

		$args = array(
			'labels' => $labels,
			'description' => __('Manage campaigns.', $this->textdomain),
			'public' => false,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'query_var' => true,
			// TODO Create an original menu icon
			//'menu_icon' => ???,
			'rewrite' => array('slug' => 'campaign'),
			'capability_type' => 'post',
			'has_archive' => true,
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('title', 'editor', 'custom-fields', 'author', 'thumbnail', 'excerpt')
		);

		register_post_type($this->post_type, $args);
	}
	

	/**
	 * Used while construct 
	 */
	public function make_shortcode($atts) {

		// FIXME ? YOU CAN call all post type by this short code :P
		$post_id = $atts['id'];
		$content = get_post($post_id);
		$code = '';

		// Not found the post
		if (empty($content)) {
			return $code;
		}

		// Check post_status is publish
		if ($content->post_status === 'publish') {
			$code = do_shortcode($content->post_content);
		}
		return $code;
	}
	

	/**
	 * TODO Enable to insert campaign easily
	 */
	public function mce_buttons() {

		$active_list = $this->get_campaigns();


		echo '<select name="hoge">';
		foreach ($active_list as $post) {
			echo '<option value="' . esc_attr($this->build_shortcode($post->ID)) . '">' . esc_html($post->post_title . '[' . $post->post_status . ']') . '</option>';
		}
		echo '</select>';
		echo '<button onclick="javascript:alert(\'TODO:カーソル位置にキャンペーンのショートコードを追加\');return false;">追加</button>';
	}
	
	/**
	 * Get campigns selectable
	 * @param Array $arg options 'post_type', 'post_status'
	 * @return array List of posts.
	 */
	private function get_campaigns($arg = array()) {

		/*
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
			'post_type' => $this->post_type,
			'post_status' => array('publish', 'pending', 'draft'),
		);
		$list = get_posts($arg);

		return $list;
	}

	private function build_shortcode($post_id) {
		$shortcode = '[wcm-show id=%d]';
		$shortcode = sprintf($shortcode, (int) $post_id);
		return $shortcode;
	}
	
	/**
	 * To display Shortcodes on campaigns list page
	 */
	public function show_tags_on_posts ()
	{
		// Table head
		add_filter(
			sprintf('manage_%s_posts_columns', $this->post_type), 
			array($this, 'show_tags_on_posts_columns') );
		
		// Column value
		add_filter(
			sprintf('manage_%s_posts_custom_column', $this->post_type), 
			array($this, 'show_tags_on_posts_custom_column'), 10, 2);
	}
	
	public function show_tags_on_posts_columns ($columns)
	{
		return array_merge($columns, array('code' => __('Short code', $this->textdomain)));
	}
	
	public function show_tags_on_posts_custom_column ($column, $post_id)
	{
		if ($column=='code') {
			_e(sprintf('<code title="Select and Copy">[wcm-show id=%d]</code>', $post_id), $this->textdomain);
		}
	}

}
