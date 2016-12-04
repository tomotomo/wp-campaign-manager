<?php
/**
 * Created by PhpStorm.
 * User: tomotomo
 * Date: 2016/12/04
 * Time: 14:22
 */

namespace tomotomobile\WPCampaignManager;

/**
 * Class ShortCode
 *
 * Specification of [wcm-show] code.
 * [wcm-show id={campaign_id}]
 * It is converted into the Campaign you registered.
 * You can find the Campaign list in the Admin Theme.
 */
class ShortCode {

	/**
	 * Used while construct
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public function behavior( $atts ) {

		// YOU CAN call all post type by this short code :P
		$post_id = $atts['id'];
		$content = get_post( $post_id );
		$code    = '';

		// Not found the post
		if ( empty( $content ) ) {
			return $code;
		}

		// Check post_status is publish
		if ( $content->post_status === 'publish' ) {
			$code = do_shortcode( $content->post_content );
		}

		return $code;
	}
}
