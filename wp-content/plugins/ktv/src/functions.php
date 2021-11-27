<?php

    require_once __DIR__ . '/admin/stream.php';

    /*
     * global vars
     * 
     */

    $ktvdb = $wpdb->prefix . 'ktv';
    $ktvdb_vers = '2.0.01';

    /* 
     * core functions
     * 
     */

    function __icon( bool $echo = false ) {

        $icon = home_url( '/favicon.png' );

        if( $echo ) echo esc_url( $icon );

        return $icon;

    }

    function __credits( bool $echo = false ) {

        $credits = sprintf(
            __( 'Copyright &copy; %s by %s. Powered by komed3.', 'ktv' ),
            date_i18n( 'Y' ),
            get_bloginfo( 'title' )
        );

        if( $echo ) echo $credits;

        return $credits;

    }

    function __active_stream() {

        global $wpdb, $ktvdb;

        return $wpdb->get_row( '
            SELECT  *
            FROM    ' . $ktvdb . '
            WHERE   tv_start < "' . date( 'Y-m-d H:i:s' ) . '"
            AND     tv_end > "' . date( 'Y-m-d H:i:s' ) . '"
        ' );

    }

    function __use_plugin( string $name ) {

        $path = __DIR__ . '/plugins/' . $name . '.php';

        if( file_exists( $path ) )
            require_once $path;
        
        else die( 'ERROR: plugin ' . $name . ' not found.' );

    }

    function __get_stream( int $ID ) {

        global $wpdb, $ktvdb;

        return $wpdb->get_row( '
            SELECT  *
            FROM    ' . $ktvdb . '
            WHERE   tv_id = ' . $ID
        );

    }

    function __update_stream(
        int $ID,
        string $video_id,
        string $lang,
        int $start,
        int $end,
        bool $vod
    ) {

        global $wpdb, $ktvdb;

        return $wpdb->query( '
            INSERT INTO ' . $ktvdb . ' (
                tv_id, tv_stream, tv_lang, tv_start, tv_end, tv_vod
            ) VALUES (
                "' . $ID . '",
                "' . $video_id . '",
                "' . $lang . '",
                "' . date( 'Y-m-d H:i:s', $start ) . '",
                "' . date( 'Y-m-d H:i:s', $end ) . '",
                "' . intval( $vod ) . '"
            ) ON DUPLICATE KEY UPDATE
                tv_stream = "' . $video_id . '",
                tv_lang =   "' . $lang . '",
                tv_start =  "' . date( 'Y-m-d H:i:s', $start ) . '",
                tv_end =    "' . date( 'Y-m-d H:i:s', $end ) . '",
                tv_vod =    "' . intval( $vod ) . '"
        ' );

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
     * install / upgrade Komed TV plugin
     * 
     */

    function __upgrade() {

        global $wpdb, $ktvdb, $ktvdb_vers;

        if( get_option( '__ktvdb_vers', 0 ) != $ktvdb_vers ) {
            
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $wpdb->query( '
                CREATE TABLE IF NOT EXISTS ' . $ktvdb . ' (
                    tv_id int NOT NULL,
                    tv_stream varbinary(32) NOT NULL,
                    tv_lang varbinary(8) NOT NULL,
                    tv_start timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    tv_end timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    tv_vod tinyint(1) NOT NULL DEFAULT "0"
                ) ' . $wpdb->get_charset_collate()
            );

            $wpdb->query( '
                ALTER TABLE ' . $ktvdb . '
                    ADD PRIMARY KEY ( tv_id );
            ' );

            update_option( '__ktvdb_vers', $ktvdb_vers );

        }

    }

    add_action( 'plugins_loaded', '__upgrade' );

?>