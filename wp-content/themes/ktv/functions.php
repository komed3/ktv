<?php

    add_action( 'init', function () {

        /* remove unused code */

        remove_action( 'wp_head', 'wp_generator' );
        remove_action( 'wp_head', 'wlwmanifest_link' );
        remove_action( 'wp_head', 'feed_links', 2 );
        remove_action( 'wp_head', 'feed_links_extra', 3 );
        remove_action( 'wp_head', 'rsd_link' );
        remove_action( 'wp_head', 'wp_shortlink_wp_head' );
        remove_action( 'wp_head', 'adjacent_posts_rel_link' );
        remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
        remove_action( 'wp_head', 'wp_oembed_add_host_js' );
        remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
        remove_action( 'rest_api_init', 'wp_oembed_register_route' );
        remove_filter( 'pre_oembed_result', 'wp_filter_pre_oembed_result', 10 );
        remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );

        add_filter( 'embed_oembed_discover', '__return_false' );

        add_filter( 'tiny_mce_plugins', function ( $plugins ) {

            return array_diff( $plugins, [ 'wpembed' ] );

        } );

        add_filter( 'rewrite_rules_array', function ( $rules ) {

            foreach( $rules as $rule => $rewrite ) {

                if( false !== strpos( $rewrite, 'embed=true' ) ) {
                    unset( $rules[ $rule ] );
                }

            }

            return $rules;

        } );

        /* remove SVG filters */

        remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
        remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );

        /* disable TinyMCE emojis */

        remove_action( 'admin_print_styles', 'print_emoji_styles' );
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
        remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
        remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

        /* filter to remove TinyMCE emojis */

        add_filter( 'tiny_mce_plugins', function ( $plugins ) {

            if( is_array( $plugins ) ) {

                return array_diff( $plugins, [ 'wpemoji' ] );

            } else {

                return [];

            }

        } );

    } );

    add_action( 'after_setup_theme', function () {

        /* add theme support */

        add_theme_support( 'title-tag' );

        /* remove theme support */

        remove_theme_support( 'post-formats' );

    } );

    add_action( 'wp_enqueue_scripts', function () {

        /* remove unused scripts */

        wp_dequeue_script( 'wp-embed' );

        /* remove unused styles */

        wp_dequeue_style( 'global-styles' );
        wp_dequeue_style( 'classic-theme-styles' );
        wp_dequeue_style( 'wp-block-library' );
        wp_dequeue_style( 'wp-block-library-theme' );
        wp_dequeue_style( 'wc-blocks-style' );

    } );

?>