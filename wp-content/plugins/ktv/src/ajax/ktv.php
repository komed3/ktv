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

                if( empty( $stream = __get_stream( $_POST['data']['vid'] ?? $_POST['data']['request'] ?? 0 ) ) ||
                    empty( $post = get_post( $stream->tv_id ?? 0 ) ) ||
                    empty( $viewer = __stream_viewer( $stream ) ) ) {

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
                $title = get_the_title( $post );

                $content = $viewer . __stream_info( $stream, $post ) . '<div class="content">
                    ' . __stream_grid( $wpdb->get_results( '
                        SELECT   *
                        FROM     ' . $ktvdb . '
                        WHERE    tv_id IN ( ' . implode( ', ', array_column( get_posts( [
                            'post_type' => 'stream',
                            'numberposts' => -1,
                            'post__not_in' => [ $post->ID ]
                        ] ), 'ID' ) ) . ' )
                        ORDER BY tv_start DESC
                        LIMIT    0, 3
                    ' ) ) . '
                </div>';

                break;

            case 'schedule':

                $page = 'schedule';
                $url = 'schedule';
                $refresh = 90000;
                $title = __( 'Schedule', 'bm' );

                $content = '<div class="content">
                    <h2 class="page-title">' . __( 'Schedule', 'bm' ) . '</h2>
                    ...
                </div>';

                break;

            case 'vod':

                $page = 'vod';
                $url = 'vod';
                $refresh = 600000;
                $title = __( 'Videos on demand', 'bm' );

                $content = '<div class="content">
                    <h2 class="page-title">' . $title . '</h2>
                    ' . __stream_grid( $wpdb->get_results( '
                        SELECT   *
                        FROM     ' . $ktvdb . '
                        WHERE    tv_id IN ( ' . implode( ', ', array_column( get_posts( [
                            'post_type' => 'stream',
                            'numberposts' => -1
                        ] ), 'ID' ) ) . ' )
                        AND      tv_start < NOW()
                        ORDER BY tv_start DESC
                        LIMIT    0, 999
                    ' ) ) . '
                </div>';

                break;

            case 'channel':
            case 'tag':
            case 'topic':

                $type = $_POST['data']['page'] == 'channel' ? 'category' : 'post_tag';

                if( empty( $term = get_term_by(
                    'slug',
                    $_POST['data']['channel'] ?? $_POST['data']['tag'] ??
                    $_POST['data']['topic'] ?? $_POST['data']['request'] ?? '',
                    $type
                ) ) ) {

                    echo json_encode( [
                        'redirect' => [
                            'page' => 'vod'
                        ]
                    ], JSON_NUMERIC_CHECK );

                    wp_die();

                }

                $page = $_POST['data']['page'] == 'channel' ? 'channel' : 'topic';
                $url = $page . '/' . $term->slug;
                $refresh = 600000;
                $title = sprintf(
                    $_POST['data']['page'] == 'channel'
                        ? __( '@%s', 'bm' )
                        : __( 'Topic: %s', 'bm' ),
                    $term->name
                );

                $content = '<div class="content">
                    <h2 class="page-title">
                        ' . ( $_POST['data']['page'] == 'channel'
                            ? __( 'Channel', 'bm' )
                            : __( 'Topic', 'bm' )
                        ) . '
                        <span>' . $term->name . '</span>
                    </h2>
                    ' . __stream_grid( $wpdb->get_results( '
                        SELECT   *
                        FROM     ' . $ktvdb . '
                        WHERE    tv_id IN ( ' . implode( ', ', array_column( get_posts( [
                            'post_type' => 'stream',
                            'numberposts' => -1,
                            $type => $term->slug
                        ] ), 'ID' ) ) . ' )
                        ORDER BY tv_start DESC
                        LIMIT    0, 999
                    ' ) ) . '
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