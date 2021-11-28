<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="icon" href="<?php __icon( true ); ?>">
        <?php wp_head(); ?>
    </head>
    <body <?php body_class(); ?>>
        <header id="header" class="clearfix" role="banner">
            <div class="header-inner">
                <h1 class="site-title">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <img src="<?php __icon( true ); ?>" />
                        <span><?php bloginfo( 'title' ); ?></span>
                    </a>
                </h1>
                <?php if( !is_front_page() && __active_stream() ) { ?>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="on-air">
                        <?php _e( 'on air', 'ktv' ) ?>
                    </a>
                <?php } ?>
                <div class="clear">&nbsp;</div>
                <nav role="navigation">
                    <?php wp_nav_menu( [
                        'theme_location' => 'primary',
                        'container' => '',
                        'fallback_cb' => false
                    ] ); ?>
                </nav>
            </div>
        </header>
        <main class="clearfix">