<?php

    function __output_stream( $stream ) {

        $post = get_post( $stream->tv_id );
        $terms = __stream_terms( $stream, $post );
        $tags = __stream_tags( $stream, $post );

        echo '<div class="stream">
                <div class="video-container">
                    <iframe class="video"
                        src="https://www.youtube.com/embed/' . $stream->tv_stream . '"
                        frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen></iframe>
                </div>
                <div class="info-container">
                    <div class="time" data-timestamp="' . ( strtotime( $stream->tv_end ) - time() ) . '"></div>
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