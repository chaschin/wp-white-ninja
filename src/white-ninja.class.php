<?php

/**
 * WP White Ninja
 *
 * @package    WordPress
 * @subpackage WPWhiteNinja
 */

use White_Ninja\Traits\Singleton;

/**
 * White_Ninja class
 */
class White_Ninja {

    use Singleton;

    /**
     * Initialization
     */
    private function __construct() {
        global $sitepress;

        // Disable WP Generator info
        remove_action( 'wp_head', 'wp_generator' );

        // Move scripts from header to footer
        add_action( 'wp_enqueue_scripts', [ $this, 'remove_head_scripts' ] );
        
        // Disable WPML meta information
        if ( $sitepress ) {
            remove_action( 'wp_head', [ $sitepress, 'meta_generator_tag' ] );
        }
        
        // Disable Yoast SEO Premium plugin meta information

        // Disable the emoji's
        add_action( 'init', [ $this, 'disable_emojis' ] );

        
        // Remove X-Pingback http header
        add_filter( 'xmlrpc_enabled', '__return_false' );
        add_filter( 'wp_headers', function( $headers, $wp_query ) {
            if ( array_key_exists( 'X-Pingback', $headers ) ) {
                unset( $headers['X-Pingback'] );
            }
            return $headers;
        }, 11, 2 );

        // Remove rel=”pingback” meta tag
        add_filter( 'bloginfo_url', function( $output, $property ) {
            error_log( "====property=" . $property );
            return ( $property == 'pingback_url' ) ? null : $output;
        }, 11, 2 );

        // Remove EditUri meta tag
        add_action( 'wp', function() {
            remove_action( 'wp_head', 'rsd_link' );
        }, 11 );

        // Remove wlwmanifest from WordPress
        remove_action( 'wp_head', 'wlwmanifest_link' );

        // Disable RSS Feeds
        remove_action( 'wp_head', 'feed_links_extra', 3 );
        remove_action( 'wp_head', 'feed_links', 2 );

        // Remove Gutenberg Block Library CSS from loading on the frontend
        add_action( 'wp_enqueue_scripts', [ $this, 'smartwp_remove_wp_block_library_css' ], 100 );

        // Remove api.w.org REST API from WordPress header
        remove_action( 'wp_head', 'rest_output_link_wp_head' );
        remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
        remove_action( 'template_redirect', 'rest_output_link_header', 11 );
    }

    /**
     * Plugin activation hook
     */
    public function activation() {
    }

    /**
     * Plugin deactivation hook
     *
     * @return void
     */
    public function deactivation() {
    }

    /**
     * Remove Gutenberg Block Library CSS from loading on the frontend
     *
     * @return void
     */
    public function smartwp_remove_wp_block_library_css() {
        wp_dequeue_style( 'wp-block-library' );
        wp_dequeue_style( 'wp-block-library-theme' );
        wp_dequeue_style( 'wc-block-style' ); // Remove WooCommerce block CSS
    } 

    /**
     * Disable the emoji's
     */
    public function disable_emojis() {
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_action( 'admin_print_styles', 'print_emoji_styles' ); 
        remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
        remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); 
        remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

        add_filter( 'tiny_mce_plugins', [ $this, 'disable_emojis_tinymce' ] );
        add_filter( 'wp_resource_hints', [ $this, 'disable_emojis_remove_dns_prefetch' ], 10, 2 );
    }

    /**
     * Filter function used to remove the tinymce emoji plugin.
     * 
     * @param array $plugins 
     * @return array Difference betwen the two arrays
     */
    public function disable_emojis_tinymce( $plugins ) {
        if ( is_array( $plugins ) ) {
            return array_diff( $plugins, [ 'wpemoji' ] );
        } else {
            return array();
        }
    }

    /**
     * Remove emoji CDN hostname from DNS prefetching hints.
     *
     * @param array $urls URLs to print for resource hints.
     * @param string $relation_type The relation type the URLs are printed for.
     * @return array Difference betwen the two arrays.
     */
    function disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
        if ( 'dns-prefetch' == $relation_type ) {
            /** This filter is documented in wp-includes/formatting.php */
            $emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
            $urls = array_diff( $urls, [ $emoji_svg_url ] );
        }
        return $urls;
    }

    /**
     * Move scripts from header to footer
     *
     * @return void
     */
    public function remove_head_scripts() {
        remove_action( 'wp_head', 'wp_print_scripts' );
        remove_action( 'wp_head', 'wp_print_head_scripts', 9 );
        remove_action( 'wp_head', 'wp_enqueue_scripts', 1 );

        add_action( 'wp_footer', 'wp_print_scripts', 5 );
        add_action( 'wp_footer', 'wp_enqueue_scripts', 5 );
        add_action( 'wp_footer', 'wp_print_head_scripts', 5 );
    }

}
