<?php

    add_action( 'init', function () {

        register_post_type( 'stream', [
            'label' => __( 'Streams', 'ktv' ),
            'description' => __( 'Streams', 'ktv' ),
            'labels' => [
                'name' => __( 'Streams', 'ktv' ),
                'singular_name' => __( 'Stream', 'ktv' ),
                'menu_name' => __( 'Streams', 'ktv' ),
                'all_items' => __( 'All streams', 'ktv' ),
                'view_item' => __( 'View stream', 'ktv' ),
                'add_new_item' => __( 'Add new stream', 'ktv' ),
                'edit_item' => __( 'Edit stream', 'ktv' ),
                'update_item' => __( 'Update stream', 'ktv' ),
                'search_items' => __( 'Search streams', 'ktv' )
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
                'category', 'post_tag'
            ],
            'menu_icon' => 'dashicons-video-alt3'
        ] );

        flush_rewrite_rules();

    } );

    add_action( 'edit_form_after_editor', function ( $post ) {

        if( $post->post_type == 'stream' ) {

            $stream = __get_stream( $post->ID );

            $start = explode( ' ', $stream ? $stream->tv_start : date( 'Y-m-d H:00:00' ) );
            $end = explode( ' ', $stream ? $stream->tv_end : date( 'Y-m-d H:59:00' ) );

            ?><div class="clearfix">&nbsp;</div>
            <div id="stream-settings" class="postbox">
                <div class="postbox-header">
                    <h2><?php _e( 'Stream settings', 'ktv' ); ?></h2>
                </div>
                <div class="inside">
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th><label for="stream"><?php _e( 'Stream (VideoID)', 'ktv' ); ?></label></th>
                                <td><input type="text" name="stream" id="stream" class="regular-text" value="<?php
                                    echo ( $stream ? $stream->tv_stream : '' );
                                ?>" /></td>
                            </tr>
                            <tr>
                                <th><label for="lang"><?php _e( 'Language', 'ktv' ); ?></label></th>
                                <td><input type="text" name="lang" id="lang" class="regular-text" value="<?php
                                    echo ( $stream ? $stream->tv_lang : '' );
                                ?>" /></td>
                            </tr>
                            <tr>
                                <th><label for="start_date"><?php _e( 'Start time (UTC)', 'ktv' ); ?></label></th>
                                <td>
                                    <input type="date" name="start_date" id="start_date" class="regular-text" value="<?php
                                        echo $start[0];
                                    ?>" />
                                    <input type="time" name="start_time" id="start_time" class="regular-text" value="<?php
                                        echo $start[1];
                                    ?>" />
                                </td>
                            </tr>
                            <tr>
                                <th><label for="end_date"><?php _e( 'End time (UTC)', 'ktv' ); ?></label></th>
                                <td>
                                    <input type="date" name="end_date" id="end_date" class="regular-text" value="<?php
                                        echo $end[0];
                                    ?>" />
                                    <input type="time" name="end_time" id="end_time" class="regular-text" value="<?php
                                        echo $end[1];
                                    ?>" />
                                </td>
                            </tr>
                            <tr>
                                <th><label for="vod"><?php _e( 'VOD available?', 'ktv' ); ?></label></th>
                                <td>
                                    <input type="checkbox" name="vod" id="vod" class="regular-text" value="1" <?php
                                        if( $stream && $stream->tv_vod ) { ?>checked<?php }
                                    ?> />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div><?php

        }

    } );

    /*function __save_stream( $post_id ) {

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

    add_action( 'delete_post', '__delete_stream' );*/

?>