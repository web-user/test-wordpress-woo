<?php
/**
 * Plugin Name:       Stripe custom payments
 * Plugin URI:        https://github.com/web-user/custom-shop-wordpress
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Volodymyr
 * Author URI:        https://github.com/web-user/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       stripe-custom-payments
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}


/**
 * The code that runs during plugin activation.
 */
function activate_plugin_name() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-stripe-custom-payments-activator.php';
    Stripe_Custom_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_plugin_name() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-stripe-custom-payments-deactivator.php';
    Stripe_Custom_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_plugin_name' );
register_deactivation_hook( __FILE__, 'deactivate_plugin_name' );


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-stripe-custom-payments.php';




function run_plugin_name() {

    $plugin = new Stripe_Custom();
    $plugin->run();

}
run_plugin_name();