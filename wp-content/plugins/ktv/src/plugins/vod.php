<?php

    function __vod( int $limit = 99 ) {

        global $wpdb, $ktvdb;

        $vods = [];

        foreach( $wpdb->get_results( '
            SELECT   *
            FROM     ' . $ktvdb . '
            WHERE    tv_vod = 1
            AND      tv_end < "' . date( 'Y-m-d H:i:s' ) . '"
            ORDER BY tv_end DESC
            LIMIT    0, ' . $limit
        ) as $stream ) {

            $vods[] = __vod_video( $stream );

        }

        __use_style( 'vod' );

        echo '<div class="vod">
            <h2>' . __( 'VOD', 'ktv' ) . '</h2>
            <div class="videos">
                ' . implode( '', $vods ) . '
            </div>
        </div>';

    }

    function __vod_video( $stream ) {

        $post = get_post( $stream->tv_id );

        return '<a href="' . get_permalink( $post ) . '">
            <div class="video">
                ' . __stream_img( $stream ) . '
                <div class="info">
                    <div class="time">' . wp_date(
                        __( 'm/d/Y, h:i A', 'ktv' ),
                        strtotime( $stream->tv_start )
                    ) . '</div>
                    <h3>' . get_the_title( $post ) . '</h3>
                    <p>' . implode( ' ', array_slice( explode( ' ', $post->post_content ), 0, 20 ) ) . ' &hellip;</p>
                </div>
            </div>
        </a>';

    }

?>