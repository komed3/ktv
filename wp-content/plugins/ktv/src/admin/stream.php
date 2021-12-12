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
                'channel', 'category', 'post_tag'
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

    function __stream_editor( $post ) {

        if( $post->post_type == 'stream' ) {

            $stream = __get_stream( $post->ID );

            $start = explode( ' ', $stream ? $stream->tv_start : date( 'Y-m-d H:00:00' ) );
            $end = explode( ' ', $stream ? $stream->tv_end : date( 'Y-m-d H:59:00' ) );

            echo '<div class="clearfix">&nbsp;</div>
            <div id="stream-settings" class="postbox">
                <div class="postbox-header">
                    <h2>' . __( 'Stream settings', 'ktv' ) . '</h2>
                </div>
                <div class="inside">
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th><label for="stream">' . __( 'Stream (VideoID)', 'oipm' ) . '</label></th>
                                <td><input type="text" name="stream" id="stream" class="regular-text" value="' .
                                    ( $stream ? $stream->tv_stream : '' ) . '" /></td>
                            </tr>
                            <tr>
                                <th><label for="lang">' . __( 'Language', 'oipm' ) . '</label></th>
                                <td><input type="text" name="lang" id="lang" class="regular-text" value="' .
                                    ( $stream ? $stream->tv_lang : '' ) . '" /></td>
                            </tr>
                            <tr>
                                <th><label for="start_date">' . __( 'Start time (UTC)', 'oipm' ) . '</label></th>
                                <td>
                                    <input type="date" name="start_date" id="start_date" class="regular-text" value="' . $start[0] . '" />
                                    <input type="time" name="start_time" id="start_time" class="regular-text" value="' . $start[1] . '" />
                                </td>
                            </tr>
                            <tr>
                                <th><label for="end_date">' . __( 'End time (UTC)', 'oipm' ) . '</label></th>
                                <td>
                                    <input type="date" name="end_date" id="end_date" class="regular-text" value="' . $end[0] . '" />
                                    <input type="time" name="end_time" id="end_time" class="regular-text" value="' . $end[1] . '" />
                                </td>
                            </tr>
                            <tr>
                                <th><label for="vod">' . __( 'VOD available?', 'oipm' ) . '</label></th>
                                <td>
                                    <input type="checkbox" name="vod" id="vod" class="regular-text" value="1" ' . (
                                        $stream && $stream->tv_vod ? 'checked' : ''
                                    ) . ' />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>';

        }

    }

    add_action( 'edit_form_after_editor', '__stream_editor' );

    function __save_stream( $post_id ) {

        if( $post_id && count( $_POST ) > 0 ) {

            $post = get_post( $post_id );

            if( $post->post_type === 'stream' ) {

                __update_stream(
                    $post_id,
                    $_POST['stream'],
                    $_POST['lang'],
                    strtotime( $_POST['start_date'] . ' ' . $_POST['start_time'] ),
                    strtotime( $_POST['end_date'] . ' ' . $_POST['end_time'] ),
                    isset( $_POST['vod'] )
                );

            }

        }

    }

    add_action( 'save_post', '__save_stream' );

    function __delete_stream( $postid ) {

        global $wpdb, $ktvbd;

        $query->query( '
            DELETE FROM ' . $ktvbd . '
            WHERE       tv_id = ' . $postid
        );

    }

    add_action( 'delete_post', '__delete_stream' );

?>