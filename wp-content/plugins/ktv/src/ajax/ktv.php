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

                $content = $viewer . __stream_info( $stream, $post );

                break;

            case 'channel':

                if( empty( $channel = get_term_by(
                    'slug', $_POST['data']['channel'] ?? $_POST['data']['request'] ?? '', 'category'
                ) ) ) {

                    echo json_encode( [
                        'redirect' => [
                            'page' => 'vod'
                        ]
                    ], JSON_NUMERIC_CHECK );

                    wp_die();

                }

                $page = 'channel';
                $url = 'channel/' . $channel->slug;
                $refresh = -1;
                $title = sprintf(
                    __( 'Channel: %s', 'bm' ),
                    $channel->name
                );

                $content = '<div class="content">
                    <h2 class="page-title">
                        ' . __( 'Channel', 'bm' ) . '
                        <span>' . $channel->name . '</span>
                    </h2>
                    ' . __stream_grid( get_posts( [
                        'post_type' => 'stream',
                        'numberposts' => 999,
                        'category_name' => $channel->slug
                    ] ) ) . '
                </div>';

                break;

            case 'tag':
            case 'topic':

                if( empty( $topic = get_term_by(
                    'slug', $_POST['data']['tag'] ?? $_POST['data']['topic'] ??
                    $_POST['data']['request'] ?? '', 'post_tag'
                ) ) ) {

                    echo json_encode( [
                        'redirect' => [
                            'page' => 'vod'
                        ]
                    ], JSON_NUMERIC_CHECK );

                    wp_die();

                }

                $page = 'topic';
                $url = 'topic/' . $topic->slug;
                $refresh = -1;
                $title = sprintf(
                    __( 'Topic: %s', 'bm' ),
                    $topic->name
                );

                $content = '<div class="content">
                    <h2 class="page-title">
                        ' . __( 'Topic', 'bm' ) . '
                        <span>' . $topic->name . '</span>
                    </h2>
                    ' . __stream_grid( get_posts( [
                        'post_type' => 'stream',
                        'numberposts' => 999,
                        'tag' => $topic->slug
                    ] ) ) . '
                </div>';

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

                            return __stream_box( $stream );

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