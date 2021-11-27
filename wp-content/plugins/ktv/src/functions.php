<?php

    /*
     * global vars
     * 
     */

    $ktvdb = $wpdb->prefix . 'ktv';
    $ktvdb_vers = '2.0';

    /* 
     * core functions
     * 
     */

    function __icon( bool $echo = false ) {

        $icon = home_url( '/favicon.png' );

        if( $echo ) echo esc_url( $icon );

        return $icon;

    }

    /* 
     * initiate Komed TV plugin
     * 
     */

    function __init() {

        # disable password recovery

        if( !is_admin() ) {

          add_filter( 'allow_password_reset', '__return_false' );

        }

        # remove dashboard access

        if( is_admin() && !defined( 'DOING_AJAX' ) &&
            !current_user_can( 'manage_options' ) ) {

            wp_redirect( home_url() );
            exit;

        }

    }

    add_action( 'init', '__init' );

    /* 
     * install Komed TV plugin
     * 
     */

    function __upgrade() {

        global $wpdb, $ktvdb, $ktvdb_vers;

        if( get_option( '__ktvdb_vers', 0 ) != $ktvdb_vers ) {
            
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            dbDelta( 'CREATE TABLE ' . $ktvdb . ' (
                tv_id int NOT NULL,
                tv_stream varbinary(32) NOT NULL,
                tv_lang varbinary(8) NOT NULL,
                tv_start timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                tv_end timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                tv_vod tinyint(1) NOT NULL DEFAULT "0"
            ) ' . $wpdb->get_charset_collate() );

            update_option( '__ktvdb_vers', $ktvdb_vers );

        }

    }

    add_action( 'plugins_loaded', '__upgrade' );

?>