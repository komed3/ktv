<?php

    add_action( 'wp_ajax___ktv', function () {

        global $wpdb, $ktvdb;

        switch( $_POST['data']['page'] ?? 'live' ) {

            default:
            case 'live':

                $page = 'live';
                $url = 'live';
                $title = __( 'On Air', 'bm' );

                $content = '';

                break;

            case 'schedule':

                $page = 'schedule';
                $url = 'schedule';
                $title = __( 'Schedule', 'bm' );

                $content = '<div class="schedule">
                    ' . implode( '', array_map( function ( $stream ) use ( $ktvdb ) {
                        return '<div class="video">
                            ' . __stream_img( $stream ) . '
                        </div>';
                    }, $wpdb->get_results( '
                        SELECT   *
                        FROM     ' . $ktvdb . '
                        WHERE    tv_end > "' . date_i18n( 'Y-m-d H:i:s' ) . '"
                        ORDER BY tv_start ASC,
                                 tv_end ASC
                    ' ) ) ) . '
                </div>';

                break;

        }

        echo json_encode( [
            'page' => $page,
            'url' => $url,
            'title' => $title,
            'content' => $content
        ], JSON_NUMERIC_CHECK );

        wp_die();

    } );

?>