<?php
/*
Plugin Name: WP Campaign Manager
Plugin URI: http://wordpress.org/plugins/wp-campaign-manager/
Description: Manage your campaign banners easier.
Version: 0.2.5
Author: Tomoyuki Sugita
Author URI: http://tomotomosnippet.blogspot.jp/
License: GPLv2 or later
*/

require_once __DIR__ . '/autoloader.php';

use tomotomobile\WPCampaignManager\AdminTheme;
use tomotomobile\WPCampaignManager\ShortCode;

$instance = new WPCampaignManager();
$instance->execute();

/**
 * Class WPCampaignManager
 */
class WPCampaignManager {
	/**
	 * plugin text domain
	 * @string
	 */
	const TEXT_DOMAIN = 'wcm';

	/**
	 * @var string
	 */
	const POST_TYPE = 'wcm-campaign';

	public function execute() {
		// Initialize Custom post type.
		add_action( 'init', array( $this, 'init' ) );

		$shortCode = new ShortCode();
		// Add shortcode [wcm-show id=post_id]
		add_shortcode( 'wcm-show', array( $shortCode, 'behavior' ) );

		$adminThem = new AdminTheme(self::TEXT_DOMAIN, self::POST_TYPE);
		$adminThem->init();

	}

	/**
	 * Used while execute
	 * @todo カスタムフィールドを追加する
	 */
	public function init() {

		// Set text-domain
		load_plugin_textdomain( self::TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );


		$labels = array(
			'name'               => __( 'Campaign', self::TEXT_DOMAIN ),
			'singular_name'      => __( 'Campaign', self::TEXT_DOMAIN ),
			'add_new'            => __( 'Add New', self::TEXT_DOMAIN ),
			'add_new_item'       => __( 'Add New Campaign', self::TEXT_DOMAIN ),
			'edit_item'          => __( 'Edit Campaign', self::TEXT_DOMAIN ),
			'new_item'           => __( 'New Campaign', self::TEXT_DOMAIN ),
			'all_items'          => __( 'All Campaign', self::TEXT_DOMAIN ),
			'view_item'          => __( 'View Campaign', self::TEXT_DOMAIN ),
			'search_items'       => __( 'Search Campaigns', self::TEXT_DOMAIN ),
			'not_found'          => __( 'No Campaigns found', self::TEXT_DOMAIN ),
			'not_found_in_trash' => __( 'No Campaigns found in Trash', self::TEXT_DOMAIN ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Campaigns', self::TEXT_DOMAIN )
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Manage campaigns.', self::TEXT_DOMAIN ),
			'public'             => false,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'menu_position'      => 20,
			'menu_icon'          => 'dashicons-admin-page',
			'rewrite'            => array( 'slug' => 'campaign' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'supports'           => array( 'title', 'editor', 'custom-fields', 'author', 'thumbnail', 'excerpt' )
		);

		register_post_type( self::POST_TYPE, $args );
	}

	/**
	 * Get campaigns selectable
	 *
	 * @param array $arg options 'post_type', 'post_status'
	 *
	 * @return array List of posts.
	 */
	private function get_campaigns( $arg = array() ) {

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
		$arg  = array(
			'post_type'   => self::POST_TYPE,
			'post_status' => array( 'publish', 'pending', 'draft' ),
		);
		$list = get_posts( apply_filters( 'wcm-custom-post-arg', $arg ) );

		return $list;
	}

	/**
	 * @param $post_id
	 *
	 * @return string
	 */
	private function build_shortcode( $post_id ) {
		$shortcode = sprintf( '[wcm-show id=%d]', (int) $post_id );

		return $shortcode;
	}

}
