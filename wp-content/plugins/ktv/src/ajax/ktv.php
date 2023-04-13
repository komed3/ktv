<?php

    add_action( 'wp_ajax___ktv', function () {

        global $wpdb, $ktvdb;

        switch( $_POST['data']['page'] ?? 'live' ) {

            default:
            case 'live':

                $page = 'live';
                $url = 'live';
                $refresh = 90000;
                $title = __( 'On Air', 'bm' );

                $content = '';

                break;

            case 'watch':

                $stream = __get_stream( $_POST['data']['vid'] ?? $_POST['data']['request'] ?? 0 );
                $post = get_post( $stream->tv_id ?? 0 );

                if( empty( $stream ) || empty( $post ) ) {

                    echo json_encode( [
                        'redirect' => [
                            'page' => 'vod'
                        ]
                    ], JSON_NUMERIC_CHECK );

                    wp_die();

                }

                $page = 'watch';
                $url = 'watch/' . $stream->tv_stream;
                $refresh = -1;
                $title = sprintf(
                    __( 'Watch: %s', 'bm' ),
                    $t = get_the_title( $post )
                );

                $content = __stream_viewer( $stream ) . __stream_info( $stream, $post );

                break;

            case 'schedule':

                $page = 'schedule';
                $url = 'schedule';
                $refresh = 90000;
                $title = __( 'Schedule', 'bm' );

                $content = '<div class="content">
                    <h2 class="page-title">' . __( 'Schedule', 'bm' ) . '</h2>
                    <div class="schedule">
                        ' . implode( '', array_map( function ( $stream ) use ( $ktvdb ) {

                            return '<div class="video">
                                ' . __stream_img( $stream ) . '
                                ' . __stream_meta( $stream ) . '
                                <h3><a href="#" page="watch" vid="' . $stream->tv_stream . '">
                                    ' . get_the_title( $stream->tv_id ) . '
                                </a></h3>
                                <p>' . get_the_excerpt( $stream->tv_id ) . '</p>
                            </div>';

                        }, $wpdb->get_results( '
                            SELECT   *
                            FROM     ' . $ktvdb . '
                            WHERE    tv_end > "' . date_i18n( 'Y-m-d H:i:s' ) . '"
                            ORDER BY tv_start ASC, tv_end ASC
                        ' ) ) ) . '
                    </div>
                </div>';

                break;

        }

        echo json_encode( [
            'page' => $page,
            'url' => home_url( '/' ) . $url,
            'refresh' => $refresh,
            'title' => $title,
            'content' => $content
        ], JSON_NUMERIC_CHECK );

        wp_die();

    } );

?>