<?php

    function __init() {
        
        # disable password recovery
        
        if( !is_admin() ) {
            
            add_filter( 'allow_password_reset', '__return_false' );
            
        }
        
        # remove dashboard access
        
        if( is_admin() && !defined( 'DOING_AJAX' ) &&
            !current_user_can( 'manage_options' ) ) {
            
            wp_redirect( home_url() );
            exit;
            
        }

    }

    add_action( 'init', '__init' );

?>