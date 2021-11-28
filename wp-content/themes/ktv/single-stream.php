<?php

    if( have_posts() ) {

        the_post();
        
        if( ( $stream = __get_stream( get_the_ID() ) ) && !!$stream->tv_vod ) {

            get_header();

            __use_plugin( 'stream' );
            
            __stream( $stream );
    
            get_footer();

        } else wp_redirect( home_url( '/' ) );

    } else wp_redirect( home_url( '/' ) );

?>