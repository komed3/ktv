<?php

    /* 
     * Template Name: Stream
     * 
     */

    get_header();

    if( $active = __active_stream() ) {

        __use_plugin( 'stream' );

        __output_stream( $active );

    } else {

    }

    get_footer();

?>