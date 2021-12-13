<?php
/**
 * Anonymous function that registers a custom autoloader
 *
 * @package WordPress
 * @subpackage WPWhiteNinja
 */

spl_autoload_register(
    function ( $class ) {
        $base_dir = PLUGIN_WP_WHITE_NINJA_DIR . 'src/';
        $file = str_replace( '\\', '/', $class );
        $parts = explode( '/', $file );
        $suffix = 'class';
        if ( in_array( 'Traits', $parts ) ) {
            $suffix = 'trait';
        }
        $parts[ count( $parts ) - 1 ] = $parts[ count( $parts ) - 1 ] . '.' . $suffix . '.php';
        $file = implode( '/', $parts );
        $file = str_replace( '_', '-', strtolower( $file ) );
        if ( file_exists( $base_dir . $file ) ) {
            include_once $base_dir . $file;
        }
    }
);
