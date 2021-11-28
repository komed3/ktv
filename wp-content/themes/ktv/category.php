<?php

    $category = get_queried_object();
    
    if( count( $posts = get_posts( [
        'post_type' => 'stream',
        'post_status' => 'publish',
        'tax_query' => [ [
            'taxonomy' => $category->taxonomy,
            'field' => 'term_id',
            'terms' => $category->term_id
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
            <span class="key">' . __( 'Category', 'ktv' ) . '</span>
            <span class="val">' . $category->name . '</span>
        </h2>
        <div class="videos">';

    foreach( $posts as $post ) {

        echo __vod_video( __get_stream( $post->ID ) );

    }

    echo '</div></div>';

    get_footer();

?>