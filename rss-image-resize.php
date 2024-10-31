<?php

/**
 *
 * @link              https://wpalchemists.com
 * @since             2.0.0
 * @package           Rss_Image_Resize
 *
 * @wordpress-plugin
 * Plugin Name:       RSS Image Resize
 * Plugin URI:        https://wpalchemists.com/plugins
 * Description:       Resize images in RSS feeds so they will fit in HTML emails. 
 * Version:           2.0.0
 * Author:            Morgan Kay
 * Author URI:        https://wpalchemists.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rss-image-resize
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * We don't need to do anything on activation, but I'm leaving this here in case it is needed later
 */
// function activate_rss_image_resize() {
// 	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rss-image-resize-activator.php';
// 	Rss_Image_Resize_Activator::activate();
// }

/**
 * We don't need to do anything on deactivation, but I'm leaving this here in case it is needed later
 */
// function deactivate_rss_image_resize() {
// 	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rss-image-resize-deactivator.php';
// 	Rss_Image_Resize_Deactivator::deactivate();
// }

// register_activation_hook( __FILE__, 'activate_rss_image_resize' );
// register_deactivation_hook( __FILE__, 'deactivate_rss_image_resize' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-rss-image-resize.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.0
 */
function run_rss_image_resize() {

	$plugin = new Rss_Image_Resize();
	$plugin->run();

}
run_rss_image_resize();
