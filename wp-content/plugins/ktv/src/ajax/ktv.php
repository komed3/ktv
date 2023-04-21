<?php

    add_action( 'wp_ajax___ktv', function () {

        global $wpdb, $ktvdb;

        switch( $_POST['data']['page'] ?? 'live' ) {

            default:
            case 'live':

                $page = 'live';
                $url = 'live';
                $refresh = 60000;
                $title = __( 'Broadcasts. Livestreams. VOD.', 'ktv' );
                $exclude = [];

                if( !empty( $stream = __active_stream() ) &&
                    !empty( $post = get_post( $stream->tv_id ) ) &&
                    !empty( $viewer = __stream_viewer( $stream ) ) ) {

                    $title = sprintf( __( 'On Air — %s', 'ktv' ), get_the_title( $stream->tv_id ) );
                    $exclude[] = $post->ID;

                    $content = $viewer . __stream_info( $stream, $post );

                } else if( $stream = $wpdb->get_row( '
                    SELECT   *
                    FROM     ' . $ktvdb . '
                    WHERE    tv_start > "' . date_i18n( 'Y-m-d H:i:s' ) . '"
                    ORDER BY tv_start ASC
                ' ) ) {

                    $title = sprintf( __( 'Upcoming event — %s', 'ktv' ), get_the_title( $stream->tv_id ) );
                    $content = __stream_preview( $stream );

                } else {

                    $content = '<div class="stream-preview">
                        <div class="image" style="background-image: url( ./favicon_hd.png );"></div>
                        <div class="preview-info">
                            <h1>' . __( 'Welcome to k3TV', 'ktv' ) . '</h1>
                            <h2>' . __( 'Await upcoming events …', 'ktv' ) . '</h2>
                            <p>' . __( 'Currently, there’s not much to see here. Come back later and ' .
                                'we’ll give you all the great content you want.', 'ktv' ) . '</p>
                        </div>
                    </div>';

                }

                $content .= '<div class="content">
                    <h2 class="page-title">' . __( 'Previous broadcasts …', 'ktv' ) . '</h2>
                    ' . __stream_grid( $wpdb->get_results( '
                        SELECT   *
                        FROM     ' . $ktvdb . '
                        WHERE    tv_id IN ( ' . implode( ', ', array_column( get_posts( [
                            'post_type' => 'stream',
                            'numberposts' => -1,
                            'post__not_in' => $exclude
                        ] ), 'ID' ) ) . ' )
                        AND      tv_end <= NOW()
                        AND      tv_vod = 1
                        ORDER BY tv_start DESC
                        LIMIT    0, 3
                    ' ) ) . '
                </div>';

                break;

            case 'watch':

                if( empty( $stream = __get_stream( $_POST['data']['vid'] ?? $_POST['data']['request'] ?? 0 ) ) ||
                    empty( $post = get_post( $stream->tv_id ) ) ||
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
                    <h2 class="page-title">' . __( 'Previous broadcasts …', 'ktv' ) . '</h2>
                    ' . __stream_grid( $wpdb->get_results( '
                        SELECT   *
                        FROM     ' . $ktvdb . '
                        WHERE    tv_id IN ( ' . implode( ', ', array_column( get_posts( [
                            'post_type' => 'stream',
                            'numberposts' => -1,
                            'post__not_in' => [ $post->ID ]
                        ] ), 'ID' ) ) . ' )
                        AND      tv_end <= NOW()
                        AND      tv_vod = 1
                        ORDER BY tv_end DESC
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
                                    $stream->tv_start . '" end="' . $stream->tv_end . '" title="' .
                                    ( $stream_title = get_the_title( $stream->tv_id ) ) . '">
                                    <a href="#" page="watch" vid="' . $stream->tv_stream . '">
                                        <h4>' . $stream_title . '</h4>
                                        ' . ( __is_live( $stream )
                                            ? '<live>' . __( 'On Air', 'ktv' ) . '</live>'
                                            : __stream_clock( $stream )
                                        ) . '
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
                    <div class="schedule-container">
                        <div class="schedule">
                            <div class="schedule-header">
                                <div class="date">&nbsp;</div>
                                ' . implode( '', $hours ) . '
                            </div>
                            <div class="schedule-content">
                                ' . implode( '', $days ) . '
                            </div>
                        </div>
                    </div>
                </div>';

                break;

            case 'archive':
            case 'vod':

                $page = 'vod';
                $url = 'vod';
                $refresh = 600000;
                $title = __( 'Archive', 'ktv' );

                $content = '<div class="content">
                    <h2 class="page-title">' . __( 'Previous broadcasts …', 'ktv' ) . '</h2>
                    ' . __stream_grid( $wpdb->get_results( '
                        SELECT   *
                        FROM     ' . $ktvdb . '
                        WHERE    tv_id IN ( ' . implode( ', ', array_column( get_posts( [
                            'post_type' => 'stream',
                            'numberposts' => -1
                        ] ), 'ID' ) ) . ' )
                        AND      tv_start < NOW()
                        AND      tv_vod = 1
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
                        AND      tv_vod = 1
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