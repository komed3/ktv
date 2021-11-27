<?php

    /* 
     * Template Name: Stream
     * 
     */

    get_header();
    
    __use_plugin( 'stream' );
    __use_plugin( 'shedule' );

    if( $active = __active_stream() ) {

        __stream( $active );

    } else {

    }

    __shedule( 3, false );

    get_footer();

?>