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
        <header role="banner">
            ...
        </header>
        <main role="main">
            ...
        </main>
        <footer>
            ...
        </footer>
    </body>
</html>