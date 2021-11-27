<?php

    function __output_stream( $stream ) {

        $post = get_post( $stream->tv_id );
        $terms = __stream_terms( $stream, $post );
        $tags = __stream_tags( $stream, $post );

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
                    <div class="clock" time="' . ( time() - strtotime( $stream->tv_start ) ) . '"></div>
                    <div class="terms">
                        ' . implode( '', $terms ) . '
                    </div>
                    <h1>' . get_the_title( $post ) . '</h1>
                    <p>' . apply_filters( 'the_content', $post->post_content ) . '</p>
                    <div class="tags">
                        ' . implode( '', $tags ) . '
                    </div>
                </div>
            </div>
        </div>';

    }

?>