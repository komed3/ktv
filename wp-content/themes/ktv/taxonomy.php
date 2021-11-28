<?php

    $term = get_queried_object();
    $taxonomy = get_taxonomy( $term->taxonomy );

    if( count( $posts = get_posts( [
        'post_type' => 'stream',
        'post_status' => 'publish',
        'tax_query' => [ [
            'taxonomy' => $term->taxonomy,
            'field' => 'term_id',
            'terms' => $term->term_id
        ] ],
        'numberposts' => -1,
        'orderby' => 'name',
        'order' => 'ASC'
    ] ) ) == 0 ) wp_redirect( home_url( '/' ) );

    get_header();

    __use_plugin( 'vod' );

    __use_style( 'vod' );

    echo '<div class="vod">
        <h2>
            <span class="key">' . $taxonomy->labels->singular_name . '</span>
            <span class="val">' . $term->name . '</span>
        </h2>
        <div class="videos">';

    foreach( $posts as $post ) {

        echo __vod_video( __get_stream( $post->ID ) );

    }

    echo '</div></div>';

    get_footer();

?>