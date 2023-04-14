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
        <div class="progress"></div>
        <header id="header" class="clearfix" role="banner">
            <div class="header-inner">
                <a href="#" page="live" class="site-title">
                    <img src="<?php bloginfo( 'wpurl' ); ?>/favicon.png">
                    <span>k<b>3</b>TV</span>
                </a>
                <nav role="navigation">
                    <a href="#" page="live">
                        <span><?php _e( 'TV', 'ktv' ); ?></span>
                    </a>
                    <a href="#" page="schedule">
                        <span><?php _e( 'Schedule', 'ktv' ); ?></span>
                    </a>
                    <a href="#" page="vod">
                        <span><?php _e( 'VOD', 'ktv' ); ?></span>
                    </a>
                </nav>
            </div>
        </header>
        <main role="main"></main>
        <footer>
            <nav>
                <a href="#" page="live">
                    <span><?php _e( 'TV', 'ktv' ); ?></span>
                </a>
                <a href="#" page="schedule">
                    <span><?php _e( 'Schedule', 'ktv' ); ?></span>
                </a>
                <a href="#" page="vod">
                    <span><?php _e( 'VOD', 'ktv' ); ?></span>
                </a>
            </nav>
            <div class="credits">
                <?php printf(
                    __( 'Copyright © %s by %s. Powered by <a href="">komed3</a>.', 'ktv' ),
                    date_i18n( 'Y' ),
                    get_bloginfo( 'name' ),
                    'https://github.com/komed3/ktv'
                ); ?>
            </div>
            <div class="keycodes">
                <h3><?php _e( 'Keycodes', 'ktv' ); ?></h3>
                <ul class="keycodes-list">
                    <?php foreach( [
                        'M' => __( 'Mute sound', 'ktv' ),
                        'K' => __( 'Play / pause', 'ktv' ),
                        'F' => __( 'Fullscreen', 'ktv' ),
                        'ESC' => __( 'Close fullscreen', 'ktv' ),
                        '⯇' => __( 'Rewind 5 sec', 'ktv' ),
                        '⯈' => __( 'Forwards 5 sec', 'ktv' ),
                        '⯅' => __( 'Increase sound 5%', 'ktv' ),
                        '⯆' => __( 'Decrease sound 5%', 'ktv' )
                    ] as $key => $label ) { ?>
                        <li>
                            <code><?php echo $key; ?></code>
                            <span><?php echo $label; ?></span>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </footer>
    </body>
</html>