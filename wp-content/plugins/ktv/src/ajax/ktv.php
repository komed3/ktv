<?php

    add_action( 'wp_ajax___ktv', function () {

        global $wpdb, $ktvdb;

        switch( $_POST['data']['page'] ?? 'live' ) {

            default:
            case 'live':

                $page = 'live';
                $url = 'live';
                $refresh = 90000;
                $title = __( 'On Air', 'ktv' );

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
                $title = __( 'Schedule', 'ktv' );

                $curr = current_time( 'timestamp' );
                $hour = date( 'G', $curr );
                $hours = $days = [];

                for( $h = 0; $h < 24; $h += 3 ) {

                    $hours[] = '<div class="time ' . (
                        floor( $hour / 3 ) * 3 == $h ? 'current' : ''
                    ) . '">' . str_pad( $h, 2, '0', STR_PAD_LEFT ) . ':00</div>';

                }

                for( $d = strtotime( '-2 weeks midnight', $curr );
                     $d <= strtotime( '+4 weeks midnight', $curr );
                     $d += 86400 ) {

                    $date = date( 'Y-m-d', $d );

                    $days[] = '<div class="day ' . (
                        $curr >= $d && $curr <= $d + 86400 ? 'current' : ''
                    ) . '" date="' . $d . '">
                        <div class="date">
                            <span class="_day">' . date_i18n( 'D', $d ) . '</span>
                            <span class="_date">' . date_i18n( 'm/d', $d ) . '</span>
                        </div>
                        <div class="container">
                            ' . implode( '', array_map( function ( $stream ) {
                                return '<div class="event ' . ( __is_live( $stream ) ? 'live' : '' ) . '" start="' .
                                    $stream->tv_start . '" end="' . $stream->tv_end . '">
                                    <a href="#" page="watch" vid="' . $stream->tv_stream . '">
                                        <h4>' . get_the_title( $stream->tv_id ) . '</h4>
                                        ' . ( __is_live( $stream ) ? '<live>
                                            <dot></dot><span>' . __( 'Live now', 'bm' ) . '</span>
                                        </live>' : __stream_clock( $stream ) ) . '
                                    </a>
                                </div>';
                            }, $wpdb->get_results( '
                                SELECT  *
                                FROM    ' . $ktvdb . '
                                WHERE   DATE( tv_start ) = "' . $date . '"
                                OR      DATE( tv_end ) = "' . $date . '"
                            ' ) ) ) . '
                        </div>
                    </div>';

                }

                $content = '<div class="content">
                    <h2 class="page-title">' . __( 'Schedule', 'ktv' ) . '</h2>
                    <div class="schedule">
                        <div class="schedule-header">
                            <div class="date">&nbsp;</div>
                            ' . implode( '', $hours ) . '
                        </div>
                        <div class="schedule-content">
                            ' . implode( '', $days ) . '
                        </div>
                    </div>
                </div>';

                break;

            case 'vod':

                $page = 'vod';
                $url = 'vod';
                $refresh = 600000;
                $title = __( 'Videos on demand', 'ktv' );

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

                if( empty( $term = get_term_by(
                    'slug',
                    $_POST['data']['channel'] ?? $_POST['data']['tag'] ??
                    $_POST['data']['topic'] ?? $_POST['data']['request'] ?? '',
                    $_POST['data']['page'] == 'channel' ? 'category' : 'post_tag'
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
                $title = $term->name;

                $args = [
                    'post_type' => 'stream',
                    'numberposts' => -1
                ];

                $args[ [
                    0 => 'tag',
                    1 => 'category_name'
                ][
                    $_POST['data']['page'] == 'channel'
                ] ] = $term->slug;

                $content = '<div class="content">
                    <h2 class="page-title">
                        ' . ( $_POST['data']['page'] == 'channel'
                            ? __( 'Channel', 'ktv' )
                            : __( 'Topic', 'ktv' )
                        ) . '
                        <span>' . $term->name . '</span>
                    </h2>
                    ' . __stream_grid( $wpdb->get_results( '
                        SELECT   *
                        FROM     ' . $ktvdb . '
                        WHERE    tv_id IN ( ' . implode( ', ', array_column( get_posts( $args ), 'ID' ) ) . ' )
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