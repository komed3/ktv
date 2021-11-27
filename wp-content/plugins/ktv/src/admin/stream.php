<?php

    function __register_stream() {

        register_post_type( 'stream', [
            'label' => __( 'Streams', 'oipm' ),
            'description' => __( 'Streams', 'oipm' ),
            'labels' => [
                'name' => __( 'Streams', 'oipm' ),
                'singular_name' => __( 'Stream', 'oipm' ),
                'menu_name' => __( 'Streams', 'oipm' ),
                'all_items' => __( 'All streams', 'oipm' ),
                'view_item' => __( 'View stream', 'oipm' ),
                'add_new_item' => __( 'Add new stream', 'oipm' ),
                'edit_item' => __( 'Edit stream', 'oipm' ),
                'update_item' => __( 'Update stream', 'oipm' ),
                'search_items' => __( 'Search streams', 'oipm' )
            ],
            'public' => true,
            'has_archive' => true,
            'rewrite' => [
                'slug' => 'stream'
            ],
            'supports' => [
                'title', 'editor'
            ],
            'taxonomies' => [
                'channel', 'category', 'tags'
            ],
            'menu_icon' => 'dashicons-video-alt3'
        ] );

        register_taxonomy( 'channel', [ 'stream' ], [
            'labels' => [
                'name' => __( 'Channels', 'oipm' ),
                'singular_name' => __( 'Channel', 'oipm' ),
                'menu_name' => __( 'Channels', 'oipm' ),
                'search_items' =>  __( 'Search channels', 'oipm' ),
                'all_items' => __( 'All channels', 'oipm' ),
                'edit_item' => __( 'Edit channel', 'oipm' ), 
                'update_item' => __( 'Update channel', 'oipm' ),
                'add_new_item' => __( 'Add new channel', 'oipm' ),
                'new_item_name' => __( 'New channel name', 'oipm' )
            ],
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => [
                'slug' => 'channel'
            ]
        ] );

        flush_rewrite_rules();

    }

    add_action( 'init', '__register_stream' );

?>