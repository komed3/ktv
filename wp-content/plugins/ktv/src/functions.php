<?php

    require_once __DIR__ . '/admin/stream.php';
    require_once __DIR__ . '/ajax/ktv.php';

    /**
     * global vars
     * 
     */

    define( 'WP_POST_REVISIONS', false );
    define( 'AUTOSAVE_INTERVAL', 99999 );

    $ktvdb = $wpdb->prefix . 'ktv';
    $ktvdb_vers = '2.0.01';

    /**
     * stream functions
     * 
     */

    function __get_stream( int $ID ) {

        global $wpdb, $ktvdb;

        return $wpdb->get_row( '
            SELECT  *
            FROM    ' . $ktvdb . '
            WHERE   tv_id = ' . $ID
        );

    }

    function __is_stream( int $ID ) {

        global $wpdb, $ktvdb;

        return $wpdb->get_var( '
            SELECT  COUNT( tv_id )
            FROM    ' . $ktvdb . '
            WHERE   tv_id = ' . $ID
        ) == 1;

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

    function __stream_img( $stream ) {

        return !empty( $stream->tv_stream )
            ? '<div class="image" style="background-image: url( https://i.ytimg.com/vi/' .
                  $stream->tv_stream .
              '/hq720.jpg );"></div>'
            : '';

    }

    function __update_stream(
        int $ID,
        string $video_id,
        string $lang,
        int $start,
        int $end,
        bool $vod = false
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

    function __delete_stream(
        int $ID
    ) {

        global $wpdb, $ktvbd;

        $query->query( '
            DELETE FROM ' . $ktvbd . '
            WHERE       tv_id = ' . $ID
        );

    }

    /**
     * initiate Komed TV plugin
     * 
     */

    add_action( 'init', function () {

        /* login is required */

        $url = ( isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ? 'https://' : 'http://' ) .
            $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

        if( !is_user_logged_in() &&
            !( defined( 'DOING_AJAX' ) && DOING_AJAX ) &&
            !( defined( 'DOING_CRON' ) && DOING_CRON ) &&
            !( defined( 'WP_CLI' ) && WP_CLI ) &&
            !( preg_match( '/wp-login\.php/', $url ) ) ) {

            wp_safe_redirect( home_URL( '/wp-login.php' ), 302 );
            exit;

        }

        /* remove admin bar */

        add_filter( 'show_admin_bar', '__return_false' );

        /* disable password recovery */

        if( !is_admin() ) {

            add_filter( 'allow_password_reset', '__return_false' );

        }

        /* remove dashboard access */

        if( is_admin() && !defined( 'DOING_AJAX' ) &&
            !current_user_can( 'manage_options' ) ) {

            wp_redirect( home_url() );
            exit;

        }

    } );

    /**
     * install / upgrade Komed TV plugin
     * 
     */

    add_action( 'plugins_loaded', function () {

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

    } );

?>
