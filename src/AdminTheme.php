<?php
namespace tomotomobile\WPCampaignManager;

class AdminTheme {
	static $textDomain;
	static $postType;

	const NONCE_ACTION = 'wcm-nonce-save';
	const NONCE_NAME = 'wcm-nonce';
	const CUSTOM_FIELD_URL = 'wcm-url';

	public function __construct( $textDomain, $postType ) {
		self::$textDomain = $textDomain;
		self::$postType   = $postType;
	}

	public function init() {
		if ( is_admin() ) {
			$this->initCampaignsListPage();
			$this->initCampaignForm();
		}
	}

	/**
	 * To display Shortcodes on campaigns list page
	 */
	public function initCampaignsListPage() {
		// Table head
		add_filter(
			sprintf( 'manage_%s_posts_columns', self::$postType ),
			function ( $columns ) {
				return array_merge( $columns, array(
					'code'                 => __( 'Short code', self::$textDomain ),
					self::CUSTOM_FIELD_URL => 'URL'
				) );
			} );

		// Column value
		add_filter(
			sprintf( 'manage_%s_posts_custom_column', self::$postType ),
			function ( $column, $post_id ) {
				if ( $column == 'code' ) {
					_e( sprintf( '<code title="Select and Copy">[wcm-show id=%d]</code>', $post_id ), self::$textDomain );
				} elseif ( $column == self::CUSTOM_FIELD_URL ) {
					$url = get_post_meta( $post_id, self::CUSTOM_FIELD_URL, true );
					echo sprintf( '<a href="%s" target="_blank">%s</a>', esc_attr( $url ), esc_html( $url ) );
				}
			},
			10, 2 );
	}

	/**
	 * @link https://generatewp.com/snippet/68ka77b/ How to add custom fields
	 */
	private function initCampaignForm() {
		add_action( 'load-post.php', array( $this, 'initMetaBox' ) );
		add_action( 'load-post-new.php', array( $this, 'initMetaBox' ) );
	}

	public function initMetaBox() {
		add_action( 'add_meta_boxes', function () {
			add_meta_box(
				self::CUSTOM_FIELD_URL,
				__( 'Campaign Detail', self::$textDomain ),
				array( $this, 'renderMetaBox' ),
				self::$postType
			);
		} );
		add_action( 'save_post_' . self::$postType, array( $this, 'saveMetaBox' ), 10, 2 );
	}

	public function saveMetaBox( $post_id, $post ) {

		// Add nonce for security and authentication.
		$nonce        = empty( $_POST[ self::NONCE_NAME ] ) ? '' : $_POST[ self::NONCE_NAME ];
		$nonce_action = self::NONCE_ACTION;

		// Check if a nonce is set.
		if ( ! isset( $nonce ) ) {
			return;
		}
		// Check if a nonce is valid.
		if ( ! wp_verify_nonce( $nonce, $nonce_action ) ) {
			return;
		}
		// Check if it's not an autosave.
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}
		// Check if it's not a revision.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Sanitize user input.
		$new_url = isset( $_POST[ self::CUSTOM_FIELD_URL ] ) ? sanitize_text_field( $_POST[ self::CUSTOM_FIELD_URL ] ) : '';

		// Update the meta field in the database.
		update_post_meta( $post_id, self::CUSTOM_FIELD_URL, $new_url );
	}

	public function renderMetaBox( $post ) {
		// Add nonce for security and authentication.
		wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME );

		// Retrieve an existing value from the database.
		$url = get_post_meta( $post->ID, self::CUSTOM_FIELD_URL, true );

		$this->render( 'meta-box.php', array( 'url' => $url, 'field_name' => self::CUSTOM_FIELD_URL ) );
	}

	public function render( $template, $params = array() ) {
		extract( $params );
		include __DIR__ . '/../templates/' . $template;
	}
}
