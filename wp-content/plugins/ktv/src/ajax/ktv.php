<?php

    add_action( 'wp_ajax___ktv', function () {

        switch( $_POST['data']['page'] ?? 'live' ) {

            default:
            case 'live':

                $page = 'live';
                $url = 'live';
                $content = '';

                break;

        }

        echo json_encode( [
            'page' => $page,
            'url' => $url,
            'content' => $content
        ], JSON_NUMERIC_CHECK );

        wp_die();

    } );

?>