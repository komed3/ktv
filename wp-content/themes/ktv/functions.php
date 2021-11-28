<?php

    function __use_style(
        string $style,
        array $required = [ 'ktv', 'global' ]
    ) {

        wp_enqueue_style(
            $style,
            get_stylesheet_directory_uri() .
                '/src/' . $style . '.css',
            $required
        );

    }

    function __theme_styles() {

        wp_enqueue_style( 'ktv', get_stylesheet_directory_uri() . '/style.css' );

        __use_style( 'global', [ 'ktv' ] );
        __use_style( 'mobile', [ 'ktv' ] );

    }

    add_action( 'wp_enqueue_scripts', '__theme_styles' );

    function __theme_init() {

        register_nav_menus( [
            'primary' => __( 'Primary', 'ktv' ),
            'footer' => __( 'Footer', 'ktv' )
        ] );

        if( !is_admin() )
            show_admin_bar( false );

    }

    add_action( 'init', '__theme_init' );

    function __theme_setup() {

        add_theme_support( 'title-tag' );

    }

    add_action( 'after_setup_theme', '__theme_setup' );

?>