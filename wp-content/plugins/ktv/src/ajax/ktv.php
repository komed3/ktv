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

            case 'watch':

                $stream = __get_stream( $_POST['vid'] );
                $post = get_post( $stream->tv_id );

                $page = 'watch';
                $url = 'watch/' . $_POST['vid'];
                $title = sprintf(
                    __( 'Watch: %s', 'bm' ),
                    get_the_title( $post )
                );

                $content = '<div class="content"></div>';

                break;

            case 'schedule':

                $page = 'schedule';
                $url = 'schedule';
                $title = __( 'Schedule', 'bm' );

                $content = '<div class="content">
                    <h2 class="page-title">' . __( 'Schedule', 'bm' ) . '</h2>
                    <div class="schedule">
                        ' . implode( '', array_map( function ( $stream ) use ( $ktvdb ) {

                            return '<div class="video">
                                ' . __stream_img( $stream ) . '
                                <div class="meta">
                                    <div class="tag-list">
                                        ' . get_the_term_list( $stream->tv_id, 'category' ) . '
                                        <lang>' . strtoupper( $stream->tv_lang ) . '</lang>
                                    </div>
                                    ' . __stream_clock( $stream ) . '
                                </div>
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
            'title' => $title,
            'content' => $content
        ], JSON_NUMERIC_CHECK );

        wp_die();

    } );

?>