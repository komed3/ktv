<?php

    function __stream( $stream ) {

        $post = get_post( $stream->tv_id );

        $start = strtotime( $stream->tv_start );
        $end = strtotime( $stream->tv_end );

        $terms = __stream_terms( $stream, $post );
        $tags = __stream_tags( $stream, $post );

        $terms[] = $end > time()
            ? __get_live_link()
            : __get_vod_link( $post );
        
        __use_style( 'stream' );
        __use_script( 'clock' );

        echo '<div class="stream">
                <div class="video-container">
                    <iframe class="video"
                        src="https://www.youtube.com/embed/' . $stream->tv_stream . '" frameborder="0" allowfullscreen
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
                        ' . __( 'This function is not supported by your browser.', 'ktv' ) . '
                    </iframe>
                </div>
                <div class="info-container">
                    ' . ( $end > time()
                            ? '<div class="clock" time="' . ( time() - $start ) . '"></div>'
                            : '<div class="time">' . date_i18n(
                                   __( 'h:i A — F jS, Y', 'ktv' ),
                                   $start
                               ) . '</div>'
                    ) . '
                    <div class="terms">
                        ' . implode( '', $terms ) . '
                    </div>
                    <h1>' . get_the_title( $post ) . '</h1>
                    <div class="external">
                        <a href="https://www.youtube.com/watch?v=' . $stream->tv_stream . '">
                            <span>►</span>' . __( 'Watch on YouTube', 'ktv' ) . '
                        </a>
                    </div>
                    <p>' . apply_filters( 'the_content', $post->post_content ) . '</p>
                    <div class="tags">
                        ' . implode( '', $tags ) . '
                    </div>
                </div>
            </div>
        </div>';

    }

?>