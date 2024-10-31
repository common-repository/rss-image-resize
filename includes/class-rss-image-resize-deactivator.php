<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://wpalchemists.com
 * @since      2.0.0
 *
 * @package    Rss_Image_Resize
 * @subpackage Rss_Image_Resize/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      2.0.0
 * @package    Rss_Image_Resize
 * @subpackage Rss_Image_Resize/includes
 * @author     Morgan Kay <morgan@wpalchemists.com>
 */
class Rss_Image_Resize_Deactivator {

	/**
	 * Delete options.
	 *
	 * @since    2.0.0
	 */
	public static function deactivate() {
		delete_option( 'rss-image-resize-options' );

	}

}
