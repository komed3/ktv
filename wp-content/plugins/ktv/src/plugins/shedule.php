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

            $events[] = '<div class="event">
                <h3>' . get_the_title( $post ) . '</h3>
            </div>';

        }

        echo '<div class="shedule">
            ' . implode( '', $events ) . '
        </div>';

    }

?>