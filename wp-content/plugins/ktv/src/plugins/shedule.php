<?php

    function __shedule(
        int $limit = 24,
        bool $active = true
    ) {

        global $wpdb, $ktvdb;

        $events = [];

        foreach( $wpdb->get_results( '
            SELECT   *
            FROM     ' . $ktvdb . '
            WHERE    tv_' . ( $active ? 'end' : 'start' ) . ' > "' . date( 'Y-m-d H:i:s' ) . '"
            ORDER BY tv_start ASC
            LIMIT    0, ' . $limit
        ) as $stream ) {

            $post = get_post( $stream->tv_id );

            $terms = __stream_terms( $stream, $post );

            if( strtotime( $stream->tv_start ) < time() )
                $terms[] = __get_live_link();

            $events[] = '<div class="event">
                ' . ( !empty( $stream->tv_stream )
                    ? '<div class="image" style="background-image: url( https://i.ytimg.com/vi/' . $stream->tv_stream . '/hq720.jpg );"></div>'
                    : '' ) . '
                <div class="info">
                    <div class="clock" time="' . ( time() - strtotime( $stream->tv_start ) ) . '"></div>
                    <div class="terms">
                        ' . implode( '', $terms ) . '
                    </div>
                    <h3>' . get_the_title( $post ) . '</h3>
                    <p>' . implode( ' ', array_slice( explode( ' ', $post->post_content ), 0, 30 ) ) . ' &hellip;</p>
                </div>
            </div>';

        }

        __use_style( 'shedule' );
        __use_script( 'clock' );

        echo '<div class="shedule">
            <h2>' . __( 'Shedule', 'ktv' ) . '</h2>
            <div class="events">
                ' . implode( '', $events ) . '
            </div>
        </div>';

    }

?>