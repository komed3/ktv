<?php

    function __stream( $stream ) {

        $post = get_post( $stream->tv_id );

        $start = strtotime( $stream->tv_start );
        $end = strtotime( $stream->tv_end );

        $terms = __stream_terms( $stream, $post );
        $tags = __stream_tags( $stream, $post );

        if( !!$stream->tv_vod && $start < time() ) {

            $terms[] = $end > time()
                ? __get_live_link()
                : __get_vod_link( $post );

        }

        __use_style( 'stream' );
        __use_script( 'clock' );
        
        if( $end > time() ) {

            $progress = ( time() - $start ) / ( $end - $start ) * 100;
            $duration = $end - time();

            __use_script( 'reload' );

            wp_add_inline_script(
                'reload',
                'var __reload = ' . ( (int) ( $duration + 5 ) * 1000 ) . ';',
                'before'
            );

        }

        echo '<div class="stream">
                <div class="video-container">
                    ' . __stream_iframe( $stream ) . '
                </div>
                <div class="info-container">
                    ' . ( $end > time()
                            ? '<div class="progress"><div class="bar" style="width: ' . $progress . '%; animation-duration: ' . $duration . 's;"></div></div>' .
                              '<div class="clock" time="' . ( time() - $start ) . '"></div>'
                            : '<div class="time">' . wp_date(
                                   __( 'h:i A — F jS, Y', 'ktv' ),
                                   $start
                               ) . '</div>'
                    ) . '
                    <div class="terms">
                        ' . implode( '', $terms ) . '
                    </div>
                    <h1>' . get_the_title( $post ) . '</h1>
                    ' . ( !empty( $stream->tv_stream ) ? '<div class="external">
                        <a href="https://www.youtube.com/watch?v=' . $stream->tv_stream . '">
                            <span>►</span>' . __( 'Watch on YouTube', 'ktv' ) . '
                        </a>
                    </div>' : '' ) . '
                    <p>' . apply_filters( 'the_content', $post->post_content ) . '</p>
                    <div class="tags">
                        ' . implode( '', $tags ) . '
                    </div>
                </div>
            </div>
        </div>';

    }

    function __preview() {

        global $wpdb, $ktvdb;

        if( $stream = $wpdb->get_row( '
            SELECT   *
            FROM     ' . $ktvdb . '
            WHERE    tv_start > "' . date( 'Y-m-d H:i:s' ) . '"
            ORDER BY tv_start ASC
        ' ) ) {

            $post = get_post( $stream->tv_id );

            __use_style( 'preview' );

            __use_script( 'clock' );
            __use_script( 'reload' );

            wp_add_inline_script(
                'reload',
                'var __reload = ' . ( (int) ( strtotime( $stream->tv_start ) - time() + 5 ) * 1000 ) . ';',
                'before'
            );

            echo '<div class="preview">
                ' . __stream_img( $stream ) . '
                <div class="preview-container">
                    <div class="clock" time="' . ( time() - strtotime( $stream->tv_start ) ) . '"></div>
                    <div class="terms">
                        ' . implode( '', __stream_terms( $stream, $post ) ) . '
                    </div>
                    <h2>' . __( 'Coming soon …', 'ktv' ) . '</h2>
                    <h1>' . get_the_title( $post ) . '</h1>
                    <p>' . implode( ' ', array_slice( explode( ' ', $post->post_content ), 0, 30 ) ) . ' &hellip;</p>
                </div>
            </div>';

        }

    }

    function __stream_iframe( $stream ) {

        return !empty( $stream->tv_stream ) ? '<iframe class="video"
            src="https://www.youtube.com/embed/' . $stream->tv_stream . '?autoplay=1" frameborder="0" allowfullscreen
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
            ' . __( 'This function is not supported by your browser.', 'ktv' ) . '
        </iframe>' : '';

    }

?>