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

    function __get_stream( $ID ) {

        global $wpdb, $ktvdb;

        return $wpdb->get_row( '
            SELECT  *
            FROM    ' . $ktvdb . '
            WHERE   tv_id = "' . $ID . '"
            OR      tv_stream = "' . $ID . '"
        ' );

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
            WHERE   tv_start < "' . date_i18n( 'Y-m-d H:i:s' ) . '"
            AND     tv_end > "' . date_i18n( 'Y-m-d H:i:s' ) . '"
        ' );

    }

    function __is_live( $stream ) {

        $now = current_time( 'timestamp' );

        return strtotime( $stream->tv_start ) < $now &&
               strtotime( $stream->tv_end ) > $now;

    }

    function __stream_img( $stream ) {

        return !empty( $stream->tv_stream )
            ? '<div class="image" style="background-image: url( https://i.ytimg.com/vi/' .
                  $stream->tv_stream .
              '/hq720.jpg );"></div>'
            : '';

    }

    function __stream_clock( $stream ) {

        return '<clock time="' . strtotime( $stream->tv_start ) . '">&nbsp;</clock>';

    }

    function __stream_meta( $stream ) {

        $channel = get_the_terms( $stream->tv_id, 'category' )[0];

        return '<div class="stream-meta">
            <div class="tag-list">
                <a href="#" page="channel" channel="' . $channel->slug . '">' . $channel->name . '</a>
                <lang>' . strtoupper( $stream->tv_lang ) . '</lang>
            </div>
            ' . ( __is_live( $stream )
                ? '<live>' . __( 'On Air', 'ktv' ) . '</live>'
                : __stream_clock( $stream )
            ) . '
        </div>';

    }

    function __stream_viewer( $stream ) {

        if( strlen( $stream->tv_stream ) > 0 ) {

            if( __is_live( $stream ) || $stream->tv_vod ) {

                return '<div class="stream-viewer">
                    <iframe src="https://www.youtube.com/embed/' . $stream->tv_stream .
                        '?autoplay=1" frameborder="0" allowfullscreen="" allow="accelerometer; autoplay; ' .
                        'clipboard-write; encrypted-media; gyroscope; picture-in-picture">
                        ' . __( 'This function is not supported by this browser.', 'ktv' ) . '
                    </iframe>
                </div>';

            } else {

                return '<div class="stream-viewer">
                    ' . __stream_img( $stream ) . '
                </div>';

            }

        }

        return '';

    }

    function __stream_preview( $stream ) {

        return '<div class="stream-preview">
            ' . __stream_img( $stream ) . '
            <div class="preview-info">
                ' . __stream_meta( $stream ) . '
                <h2>' . __( 'Coming soon …', 'ktv' ) . '</h2>
                <h1>' . get_the_title( $stream->tv_id ) . '</h1>
                <p>' . wp_trim_words( get_the_excerpt( $stream->tv_id ) ) . '</p>
            </div>
        </div>';

    }

    function __stream_info( $stream, $post ) {

        return '<div class="stream-info">
            ' . __stream_meta( $stream ) . '
            <h2>' . get_the_title( $post ) . '</h2>
            <div class="desc">' . apply_filters( 'the_content', $post->post_content ) . '</div>
            <div class="tag-list mini">
                ' . implode( '', array_map( function ( $tag ) {
                    return '<a href="#" page="tag" tag="' . $tag->slug . '">' . $tag->name . '</a>';
                }, get_the_terms( $post->ID, 'post_tag' ) ) ) . '
            </div>
        </div>';

    }

    function __stream_box( $stream ) {

        return '<div class="video">
            ' . __stream_img( $stream ) . '
            <div class="info">
                ' . __stream_meta( $stream ) . '
                <h3><a href="#" page="watch" vid="' . $stream->tv_id . '">
                    ' . get_the_title( $stream->tv_id ) . '
                </a></h3>
                <p>' . wp_trim_words( get_the_excerpt( $stream->tv_id ), 15 ) . '</p>
            </div>
        </div>';

    }

    function __stream_grid(
        array $streams
    ) {

        return '<div class="stream-grid">
            ' . implode( '', array_map( function ( $stream ) {
                return __stream_box( $stream );
            }, $streams ) ) . '
        </div>';

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

        /* rewrite rules */

        if( $watch = get_page_by_path( 'watch' ) ) {

            add_rewrite_rule(
                '^' . $watch->post_name . '/?(.+)/?',
                'index.php?page_id=' . $watch->ID . '&vid=$matches[1]',
                'top'
            );

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
