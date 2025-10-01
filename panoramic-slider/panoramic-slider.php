<?php
/**
 * Plugin Name: Panoramic Slider Block
 * Description: Gutenberg block for ultra-wide images with horizontal scroll, drag-to-scroll, arrows, mobile swipe, adjustable height, rounded corners, and optional scrollbar.
 * Version: 1.1.0
 * Author: Brad Salomons
 * Plugin URI:  https://8r4d.com/plugins/
 * Author URI:  https://8r4d.com/
 * License:     GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function panoramic_slider_block_init() {
    // Register block editor script
    wp_register_script(
        'panoramic-slider-block',
        plugins_url( 'js/block.js', __FILE__ ), // Editor block logic
        array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components' ),
        filemtime( plugin_dir_path( __FILE__ ) . 'js/block.js' ),
        true
    );

    // Shared styles (used in editor + frontend)
    wp_register_style(
        'panoramic-slider-style',
        plugins_url( 'css/panoramic-slider.css', __FILE__ ),
        array(),
        filemtime( plugin_dir_path( __FILE__ ) . 'css/panoramic-slider.css' )
    );

    // Register the block itself
    register_block_type( 'custom/panoramic-slider', array(
        'editor_script' => 'panoramic-slider-block',
        'style'         => 'panoramic-slider-style',
    ) );
}
add_action( 'init', 'panoramic_slider_block_init' );

/**
 * Enqueue frontend-only interactivity (drag, arrows, swipe).
 */
function panoramic_slider_frontend_script() {
    if ( ! is_admin() ) { // prevent loading in editor
        wp_enqueue_script(
            'panoramic-slider-frontend',
            plugins_url( 'js/frontend.js', __FILE__ ),
            array(),
            filemtime( plugin_dir_path( __FILE__ ) . 'js/frontend.js' ),
            true
        );
    }
}
add_action( 'wp_enqueue_scripts', 'panoramic_slider_frontend_script' );
