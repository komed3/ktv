<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
        <meta name="msapplication-TileColor" content="#161d27" />
        <meta name="theme-color" content="#161d27" />
        <link rel="shortcut icon" href="<?php bloginfo( 'wpurl' ); ?>/favicon.png" />
        <?php wp_head(); ?>
    </head>
    <body <?php body_class(); ?>>
        <?php wp_body_open(); ?>
        <header id="header" class="clearfix" role="banner">
            <div class="header-inner">
                <a href="<?php echo home_url( '/' ); ?>" class="site-title">
                    <img src="<?php bloginfo( 'wpurl' ); ?>/favicon.png">
                    <span><?php bloginfo( 'name' ); ?></span>
                </a>
                <nav role="navigation">
                    <a href="<?php echo home_url( '/' ); ?>">
                        <span><?php _e( 'TV', 'bm' ); ?></span>
                    </a>
                    <a href="<?php echo home_url( '/schedule' ); ?>">
                        <span><?php _e( 'Program', 'bm' ); ?></span>
                    </a>
                    <a href="<?php echo home_url( '/vod' ); ?>">
                        <span><?php _e( 'VOD', 'bm' ); ?></span>
                    </a>
                </nav>
            </div>
        </header>
        <main role="main"></main>
        <footer>
            ...
        </footer>
    </body>
</html>