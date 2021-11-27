<?php

    /*
     * global vars
     * 
     */

    
    
    /* 
     * core functions
     * 
     */

    function __icon( bool $echo = false ) {

        $icon = home_url( '/favicon.png' );

        if( $echo ) echo esc_url( $icon );

        return $icon;

    }
    
    /* 
     * initiate Komed TV plugin
     * 
     */

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