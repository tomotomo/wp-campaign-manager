<?php
namespace tomotomobile\WPCampaignManager;

class AdminTheme {
	static $textDomain;
	static $postType;

	public function __construct($textDomain, $postType) {
		self::$textDomain = $textDomain;
		self::$postType = $postType;
	}

	public function init() {
		$this->initCampaignsListPage();
	}


	/**
	 * To display Shortcodes on campaigns list page
	 * @todo 詳細ページへのリンクを表示する
	 */
	public function initCampaignsListPage() {
		// Table head
		add_filter(
			sprintf( 'manage_%s_posts_columns', self::$postType ),
			array( $this, 'show_tags_on_posts_columns' ) );

		// Column value
		add_filter(
			sprintf( 'manage_%s_posts_custom_column', self::$postType ),
			array( $this, 'show_tags_on_posts_custom_column' ), 10, 2 );
	}

	public function show_tags_on_posts_columns( $columns ) {
		return array_merge( $columns, array( 'code' => __( 'Short code', self::$textDomain ) ) );
	}

	public function show_tags_on_posts_custom_column( $column, $post_id ) {
		if ( $column == 'code' ) {
			_e( sprintf( '<code title="Select and Copy">[wcm-show id=%d]</code>', $post_id ), self::$textDomain );
		}
	}
}
