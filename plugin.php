<?php
/**
 * WP White Ninja
 *
 * @package Wordpress
 *
 * Plugin Name: WP White Ninja
 * Description: Remove all frontend information about Wordpress and optimize pages content.
 * Version:     1.0.0
 * Author:      Alexey Chaschin
 * Author URI:  https://github.com/chaschin/wp-white-ninja
 * Text Domain: wp-white-ninja
 */

if ( ! function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

define( 'PLUGIN_WP_WHITE_NINJA_DIR', plugin_dir_path( __FILE__ ) );
define( 'PLUGIN_WP_WHITE_NINJA_URL', plugin_dir_url( __FILE__ ) );

define( 'PLUGIN_WP_WHITE_NINJA_VER', '1.0.0' );

require_once( PLUGIN_WP_WHITE_NINJA_DIR . 'src/autoload.php' );

$white_ninja = White_Ninja::get_instance();

register_activation_hook( __FILE__, [ $white_ninja, 'activation' ] );

register_deactivation_hook( __FILE__, [ $white_ninja, 'deactivation' ] );
