<?php

/**
 * @epic-charts
 * Plugin Name:       Epic Charts
 * Plugin URI:        http://www.epicwebs.co.uk/epic-charts/
 * Description:       A simple chart plugin using ChartJS.org - Allows you to create charts for the front end of your site. Create a chart using the Charts post type on the left hand side and copy the shortcode into your post or page content.
 * Version:           1.0.0
 * Author:            Rob Mehew
 * Author URI:        http://www.epicwebs.co.uk/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       epic-charts
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'epic_charts_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_epic_charts() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/epic-charts-activator.php';
	Epic_Charts_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_epic_charts() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/epic-charts-deactivator.php';
	Epic_Charts_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_epic-charts' );
register_deactivation_hook( __FILE__, 'deactivate_epic-charts' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/epic-charts-plugin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_epic_charts() {

	$plugin = new Epic_Charts();
	$plugin->run();

}
run_epic_charts();
