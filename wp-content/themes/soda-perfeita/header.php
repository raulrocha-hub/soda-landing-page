<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php bloginfo('description'); ?>">
    <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/favicon.ico">
    
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    
    <div id="page" class="site">
        <header class="site-header">
            <nav class="navbar navbar-expand-lg navbar-light bg-white">
                <div class="container">
                    <?php if (has_custom_logo()): ?>
                        <div class="navbar-brand">
                            <?php the_custom_logo(); ?>
                        </div>
                    <?php else: ?>
                        <a class="navbar-brand" href="<?php echo home_url(); ?>">
                            <strong>Soda Perfeita</strong>
                        </a>
                    <?php endif; ?>
                    
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    
                    <div class="collapse navbar-collapse" id="mainMenu">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'primary',
                            'container' => false,
                            'menu_class' => 'navbar-nav ms-auto',
                            'fallback_cb' => false,
                            'depth' => 2,
                            'walker' => new Bootstrap_NavWalker()
                        ));
                        ?>
                    </div>
                </div>
            </nav>
        </header>

        <main class="site-main">