<?php

    function __theme_init() {

        register_nav_menus( [
            'primary' => __( 'Primary', 'ktv' ),
            'footer' => __( 'Footer', 'ktv' )
        ] );

    }

    add_action( 'init', '__theme_init' );

    function __theme_setup() {

        add_theme_support( 'title-tag' );

    }

    add_action( 'after_setup_theme', '__theme_setup' );

?>