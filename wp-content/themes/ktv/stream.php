<?php

    /* 
     * Template Name: Stream
     * 
     */

    get_header();
    
    __use_plugin( 'stream' );
    __use_plugin( 'shedule' );
    __use_plugin( 'vod' );

    if( $active = __active_stream() ) {

        __stream( $active );

    } else {

        __preview();

    }

    __shedule( 3, false );

    __vod( 3 );

    get_footer();

?>